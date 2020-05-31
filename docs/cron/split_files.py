import argparse
import csv
from pathlib import Path
from sys import getsizeof


def split_file(file_path, language_index, out_dir):
    """Split a Tatoeba export file by language with the help of an 
    index that maps the sentence ids to their corresponding languages.
    """
    split_params = {"out_dir": out_dir}
    if file_path.stem == "user_languages":
        split_params.update(columns=[0])    
    elif file_path.stem == "links" and language_index:
        split_params.update(columns=[0, 1], index=language_index)
    elif file_path.stem == "tags" and language_index:
        split_params.update(columns=[0], index=language_index)
    elif file_path.stem == "sentences_in_lists" and language_index:
        split_params.update(columns=[1], index=language_index)
    elif file_path.stem == "jpn_indices" and language_index:
        split_params.update(columns=[0], index=language_index)
    elif file_path.stem == "sentences_with_audio" and language_index:
        split_params.update(columns=[0], index=language_index)
    else:
        return

    DataFile(file_path).split(**split_params)


class DataFile:
    """A file containing table data.
    """

    def __init__(self, file_path, delimiter="\t", text_col=-1):
        # the local path of this data file
        self._fp = Path(file_path)
        # the delimiter that distinguishes table columns
        self._dm = delimiter
        # the column that must not be split by delimiters
        self._tc = text_col

    def __iter__(self):

        try:
            with open(self.path) as f:
                for row in custom_reader(f, self._dm, self._tc):
                    yield row
        except FileNotFoundError:
            print("file not found at {}".format(self.path))
            return iter([])

    def index(self, key_column, value_column):
        """Maps values from two columns of this datafile.
        """
        return {
            row[key_column]: (
                "unknown" if row[value_column] == "\\N" else row[value_column]
            )
            for row in self
            if len(row) > key_column and len(row) > value_column
        }

    def split(self, columns=[], index=None, out_dir=None):
        """Split the file according to the values mapped by the index
        in a chosen set of columns. 
        """
        print("splitting {}".format(self.name))

        # init buffer
        if not out_dir:
            buffer = Buffer(self.path.parent, delimiter=self.delimiter)
        else:
            Path(out_dir).mkdir(parents=True, exist_ok=True)
            buffer = Buffer(out_dir, delimiter=self.delimiter)

        # classify the rows
        for row in self:
            mapped_fields = get_mapped_fields(row, columns, index)

            if all(mapped_fields):
                fname = self._get_out_filename(mapped_fields)
                buffer.add(row, "/".join([mapped_fields[0], fname]))

        buffer.clear()

    def _get_out_filename(self, mapped_fields):
        """Get the name of the file that corespond to this mapped fields.
        """
        mapped_fields_string = "-".join(mapped_fields)
        ext = "tsv" if self._dm == "\t" else "csv"

        return "{}_{}.{}".format(mapped_fields_string, self.stem, ext)

    @property
    def path(self):
        """Get the path of this datafile.
        """
        return self._fp

    @property
    def delimiter(self):
        """Get the string that delimitate fields in this datafile.
        """
        return self._dm

    @property
    def name(self):
        """Get the name of this datafile.
        """
        return self._fp.name

    @property
    def stem(self):
        """Get the name of this datafile without its extension.
        """
        return self._fp.stem


def unsplit_field(row, nb_cols, delimiter, index_field):
    """Regroup the chosen field of a csv row if split by mistake. Useful if
    the fields are not quoted or if extra delimiters are not escaped.
    """
    if index_field < 0:
        index_field = nb_cols + index_field

    nb_extra = len(row) - nb_cols
    if nb_extra > 0:
        fields_to_join = row[index_field : index_field + nb_extra + 1]
        row[index_field] = delimiter.join(fields_to_join)
        del row[index_field + 1 : index_field + nb_extra + 1]

    return row


def custom_reader(string_io, delimiter, text_col):
    """A customized version of csv.reader that:
    - unsplit unquoted field that contain delimiters
    - regroup unquoted multiline fields (i.e. containing newline characters)
    """
    reader = csv.reader(
        string_io, delimiter=delimiter, quoting=csv.QUOTE_NONE,
    )
    # count the number of columns in a regular row
    nb_cols = len(next(reader))
    string_io.seek(0)

    real_row = []
    for row in reader:
        # regroup text field if split by delimiter
        if text_col:
            row = unsplit_field(row, nb_cols, delimiter, text_col)

        # regroup multiline end fields
        if len(row) == nb_cols:
            if real_row:
                yield real_row
                real_row = []
            real_row.extend(row)
        elif len(row) == 1 and real_row:
            real_row[-1] += " " + row[0]
        elif not row and real_row:
            real_row[-1] += " "
        else:
            print("row skipped: {}".format(row))
    if real_row:
        yield real_row


def get_mapped_fields(row, columns, index):
    """For this row, get the values of the fields in these columns.
    If index is True, get the value mapped by the index dictionary.
    """
    mapped_fields = []
    for col in columns:
        if len(row) > col:
            val = row[col] if row[col] else 'unknown'

            if index:
                val = index.get(val)

            mapped_fields.append(val)
        else:
            mapped_fields.append("")
            print("{} does not have column {}".format(row, col))

    return mapped_fields


class Buffer:
    """A buffer temporarily stores data and then appends it into out files 
    when full. It is useful to avoid memory overflow when handling very large 
    data files.
    """

    def __init__(self, out_dir, delimiter="\t", max_size=10000):
        # directory path where out files are saved.
        self._dir = Path(out_dir)
        # the feed delimiter used in the out files
        self._dm = delimiter
        # maximum number elements in a buffer
        self._max = max_size
        # the buffer data is classified in a dict. The dict keys are named
        # after the out filenames the data is directed to.
        self._data = {}

    def add(self, elt, out_file_path):
        """Adds an element into the buffer linked to a relative out path. 
        Once the buffer is full, this element is appended to the corresponding 
        out file.
        """
        self._data.setdefault(out_file_path, []).append(elt)

        if getsizeof(self._data[out_file_path]) > self._max:
            self._save(out_file_path)

    def _save(self, out_file_path, end=False):
        """Appends buffered elements into their out datafile and then clears
        the buffer.
        """
        fp = Path(self._dir, out_file_path)
        # reinitialize the out file
        if fp.is_file():
            fp.unlink()

        data = list(self._data[out_file_path])
        part_fp = Path(self._dir, "{}.part".format(out_file_path))
        part_fp.parent.mkdir(parents=True, exist_ok=True)
        try:
            with open(part_fp, mode="a") as f:
                wt = csv.writer(f, delimiter=self._dm)
                wt.writerows(data)
        except FileNotFoundError:
            print("an error occured when opening {}".format(part_fp))
        else:
            self._data[out_file_path].clear()

            if end:
                # removes '.part' extension
                part_fp.rename(part_fp.parent.joinpath(part_fp.stem))

    def clear(self):
        """Saves the data remaining in the buffer into the corresponding 
        outfiles.
        """
        for out_file_path in self._data.keys():
            self._save(out_file_path, end=True)

        self._data.clear()


if __name__ == "__main__":

    parser = argparse.ArgumentParser(
        prog="split_mapped_file",
        description="Split Tatoeba export files by language",
    )

    parser.add_argument(
        "filenames",
        action="store",
        nargs="+",
        help="the names of the files to split",
    )

    parser.add_argument(
        "-i",
        "--indir",
        action="store",
        help="the name of the directory where the files to split are saved",
    )

    parser.add_argument(
        "-o",
        "--outdir",
        action="store",
        help="the name of the directory where split files are saved",
    )

    args = parser.parse_args()

    in_dir = Path(args.indir) if args.indir else Path.cwd()
    file_paths = [in_dir.joinpath(fn) for fn in args.filenames]
    map_path = Path(in_dir).joinpath("sentences.csv")
    out_dir = (
        Path(args.outdir)
        if args.outdir
        else Path.cwd().joinpath("per_language")
    )

    language_index = {}
    for fp in file_paths:
        if not language_index and fp.stem in (
            "links",
            "tags",
            "sentences_in_lists",
            "jpn_indices",
            "sentences_with_audio",
        ):
            language_index = DataFile(map_path).index(0, 1)

        split_file(fp, language_index, out_dir)
