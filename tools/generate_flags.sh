#!/bin/bash

# This script is used to uniformly draw 3-letter ISO codes on the side or
# center of some language icons. It operates by pasting each letter of the
# ISO code over a source flag. Each letter is generated once using
# Inkscape's "text to path" conversion function.
#
# To add a new flag that consists of an existing flag with 3-letter code
# on the side, just add another gen_flag line at the end and re-run this
# script.

set -E
# use "exit 77" from any subshell to exit the whole script
trap '[ "$?" -ne 77 ] || exit 77' ERR

has_dep() {
  which "$1" >/dev/null 2>&1
}

confirm_has_dep() {
  if ! has_dep "$1"; then
    echo "This tool requires $1, please install it"
    exit 1
  fi
}

confirm_has_font() {
  local fontfamilystyle="$1"
  confirm_has_dep fc-list
  if ! fc-list : family style|grep -q "^$fontfamilystyle$"; then
    echo "This tool requires the font $fontfamilystyle, please install it"
    exit 1
  fi
}

svg_template_letter() {
  local letter="$1"

  # about x="6.5": this could be any value high enough to keep the
  # generated letter <path> coordinates positive, so that it stays
  # inside the viewbox (which is the parent SVG viewbox, since no
  # height, width or viewBox attributes are specified).
  # 6.5 makes a good-looking letter spacing, while being "round"
  # enough to keep vertically symmetric letters (such as A, I, W etc.)
  # vertically centered on the pixel grid, which helps a bit in very
  # low res (pixelated).

  # about y="8" and font-size:9.564835164px: values calculated so that
  # the text block of capital letters (such as H or I) has an exact
  # y coordinate of 1px and an exact height of 7px.
  # The font size value is the result of 7/(1456/2048), with
  #    7 = desired capital height
  # 2048 = Roboto Mono Bold font em-square
  # 1456 = Roboto Mono Bold font capital height
  cat <<EOF
<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<svg
   xmlns="http://www.w3.org/2000/svg">
  <text
     style="font-style:normal;font-size:9.846153846px;font-family:'Roboto Mono';font-weight:bold;-inkscape-font-specification:'Roboto Mono, Bold';fill:black;text-anchor:middle"
     x="6.5"
     y="8"
     >$letter</text>
</svg>
EOF
}

# Simplify a "rotate(-90) translate($x $y)" transform
# into a "rotate(-90 n m)" transform.
rotate_90_translate() {
  local x="$1" y="$2"
  local rx=$(bc <<<"scale=2; ($y-($x))/2")
  local ry=$(bc <<<"$rx - ($y)")
  echo "rotate(-90 $rx $ry)"
}

letter2symbol() {
  local letter="$1"

  # Generate a new SVG for that letter if it does not exists
  outfile="webroot/img/flags/$letter.svg"
  if [ ! -e "$outfile" ]; then
    if ! has_dep inkscape; then
      echo "This script requires inkscape to generate $outfile" >&2
      exit 77
    fi
    svg_template_letter "$letter" \
      | inkscape --export-text-to-path \
                 --pipe \
                 --export-filename=- \
                 --export-type=svg \
      | minify_svg 3 2 \
      > "$outfile"
    echo "Generated $outfile" >&2
  fi

  # Read <svg> and print it out as <symbol id="$letter">
  svg2symbol "$letter" "$outfile"
}

gen_iso_letters() {
  local iso_code="$1" transform="$2"
  local extra symbol symbols uses

  i=0
  for letter in ${iso_code:0:1} ${iso_code:1:1} ${iso_code:2:1}; do
    # Add letter symbol (if we don't have it already)
    if [[ "$symbols" != *"id=\"$letter\""* ]]; then
      symbol=$(letter2symbol "$letter")
      symbols="$symbols$symbol"
    fi

    # Position one letter after the other
    extra=""
    if [ $i -eq 0 ]; then
      extra=' x="-13"'
    elif [ $i -eq 1 ]; then
      extra=' x="-6.5"'
    # elif [ $i -eq 2 ]; then
      # third letter is already at x="0", saving a few bytes
    fi
    uses="$uses<use$extra href=\"#$letter\"/>"

    i=$(($i+1))
  done

  echo "$symbols<g transform=\"$transform\">$uses</g>"
}

svg_template_bigfront() {
  local letters iso_code="$1"

  # Center letters by translation and scale them up to 1.5x
  # translate(15 3.25) scale(1.5)
  # = matrix(1.5 0 0 1.5 15 3.25)
  local transform="matrix(1.5 0 0 1.5 15 3.25)"
  letters=$(gen_iso_letters "$iso_code" "$transform")

  cat <<EOF
<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<svg
   width="30"
   height="20"
   xmlns="http://www.w3.org/2000/svg"
   xmlns:svg="http://www.w3.org/2000/svg">
  <path
     fill="#fff"
     d="M0 0h30v20H0z" />
  $letters
</svg>
EOF
}

svg_template_sidesmall() {
  local letters iso_code="$1" src="$2" start="$3" extra="$4"

  local id=$(basename "$src" | cut -d. -f 1)
  local symbol=$(svg2symbol "$id" "$src")
  local include="$symbol<use$extra href=\"#$id\"/>"
  local width=$((30 - $start)) # whitespace width

  # Rotate and position letters so that it ends up
  # on the white background <path> defined below.
  # The actual letters position will be
  # ($x,$y) + local coordinates in svg_template_letter()
  local x=-10
  # -7 is because letters are 7px tall
  # -1 is the y shift in svg_template_letter()
  local y=$(bc <<<"scale=2; $start + ($width-7)/2 - 1")
  local transform=$(rotate_90_translate $x $y)

  letters=$(gen_iso_letters "$iso_code" "$transform")

  cat <<EOF
<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<svg
   width="30"
   height="20"
   version="1.1"
   xmlns="http://www.w3.org/2000/svg"
   xmlns:svg="http://www.w3.org/2000/svg">
  $include
  <path
     fill="#fff"
     d="M$start 0h${width}v20H${start}z" />
  $letters
</svg>
EOF
}

strip_attr() {
  local attr="$1" tag="$2"
  if [ -z "$tag" ]; then
    sed "s/ $attr=\"[^\"]\+\"//g"
  else
    sed "s/\(<$tag[^<]*\) $attr=\"[^\"]\+\"/\1/g"
  fi
}

strip_leading_zeros() {
  sed 's/\([^0-9]\)0\+\./\1\./g'
}

compact_decimals() {
  sed ':loop;
       s/\(\.[0-9]\+\) \+\(\.\)/\1\2/g;
       t loop'
}

minify_svg() {
  local prec="${1:-5}" cprec="${2:-5}"
  scour --enable-id-stripping \
        --protect-ids-noninkscape \
        --set-precision="$prec" \
        --set-c-precision="$cprec" \
        --no-line-breaks \
        --strip-xml-space \
        --strip-xml-prolog \
        --keep-unreferenced-defs \
        2>/dev/null \
    | strip_leading_zeros \
    | compact_decimals \
    | strip_attr dominant-baseline \
    | strip_attr aria-label \
    | strip_attr encoding \
    | strip_attr version svg
}

generate_iso_svg() {
  local template

  template="$1"; shift
  svg_template_${template} "$@"
}

svg2symbol() {
  local id="$1" filename="$2"
  cat "$filename" \
    | tr -d '\n' \
    | strip_attr xmlns svg \
    | sed 's,.*<svg,<symbol,;s,</svg>,</symbol>,' \
    | sed 's,\(<symbol [^>]*\)id="[^"]*",\1,' \
    | sed "s,<symbol,\0 id=\"$id\","
}

confirm_has_dep scour
confirm_has_font "Roboto Mono:style=Bold"

gen_flag() {
  local iso_code="$1" src="$2" position="$3"
  local outfile="webroot/img/flags/${iso_code,,}.svg"
  local markup extra start

  if [ "$position" = "shrunken" ] && ! grep -q '<svg [^>]*preserveAspectRatio="none"' "$src"; then
    echo "Error generating $outfile: \"shrunken\" requires having <svg preserveAspectRatio=\"none\"> in $src"
    exit 1
  fi

  if [ -n "$src" ]; then
    start=19
    if [ "$position" = "centered" ]; then
      extra=' x="-5.5"'
    elif [ "$position" = "centeredL" ]; then
      extra=' x="-6"'
    elif [ "$position" = "shrunken" ]; then
      extra=' width="19" height="20"'
    elif [ "$position" = "squared" ]; then
      start=20
    fi
    markup=$(generate_iso_svg sidesmall "$iso_code" "$src" "$start" "$extra")
  else
    markup=$(generate_iso_svg bigfront "$iso_code")
  fi
  <<<"$markup" minify_svg > "$outfile"

  echo "Generated $outfile"
}

# Flags consisting of image + small ISO code on the side
gen_flag ASM webroot/img/flags/hin.svg centered
gen_flag BHO webroot/img/flags/hin.svg centered
gen_flag BRX webroot/img/flags/hin.svg centered
gen_flag GOM webroot/img/flags/hin.svg centered
gen_flag GUJ webroot/img/flags/hin.svg centered
gen_flag HOC webroot/img/flags/hin.svg centered
gen_flag MAI webroot/img/flags/hin.svg centered
gen_flag ORI webroot/img/flags/hin.svg centered
gen_flag PAN webroot/img/flags/hin.svg centered
gen_flag SAT webroot/img/flags/hin.svg centered
gen_flag TEL webroot/img/flags/hin.svg centered
gen_flag BER webroot/img/flags/Berber_flag.svg centeredL
gen_flag KAB webroot/img/flags/Berber_flag.svg centeredL
gen_flag RIF webroot/img/flags/Berber_flag.svg centeredL
gen_flag ZGH webroot/img/flags/Berber_flag.svg centeredL
gen_flag AOZ webroot/img/flags/ind.svg
gen_flag JAV webroot/img/flags/ind.svg
gen_flag MDR webroot/img/flags/ind.svg
gen_flag MAD webroot/img/flags/ind.svg
gen_flag TIG webroot/img/flags/Flag_of_Eritrea.svg
gen_flag CJY webroot/img/flags/cmn.svg
gen_flag GAN webroot/img/flags/cmn.svg
gen_flag HAK webroot/img/flags/cmn.svg
gen_flag HSN webroot/img/flags/cmn.svg
gen_flag MWW webroot/img/flags/cmn.svg
gen_flag NAN webroot/img/flags/cmn.svg
gen_flag WUU webroot/img/flags/cmn.svg
gen_flag AKL webroot/img/flags/tgl.svg
gen_flag BCL webroot/img/flags/tgl.svg
gen_flag BVY webroot/img/flags/tgl.svg
gen_flag CBK webroot/img/flags/tgl.svg
gen_flag CEB webroot/img/flags/tgl.svg
gen_flag CYO webroot/img/flags/tgl.svg
gen_flag HIL webroot/img/flags/tgl.svg
gen_flag ILO webroot/img/flags/tgl.svg
gen_flag LAA webroot/img/flags/tgl.svg
gen_flag WAR webroot/img/flags/tgl.svg
gen_flag AFR webroot/img/flags/Flag_of_South_Africa.svg
gen_flag XHO webroot/img/flags/Flag_of_South_Africa.svg
gen_flag ZUL webroot/img/flags/Flag_of_South_Africa.svg
gen_flag HYE webroot/img/flags/Flag_of_Armenia.svg
gen_flag HYW webroot/img/flags/Flag_of_Armenia.svg
gen_flag GLA webroot/img/flags/Flag_of_Scotland.svg shrunken
gen_flag SCO webroot/img/flags/Flag_of_Scotland.svg shrunken
gen_flag BOM webroot/img/flags/Flag_of_Nigeria.svg shrunken
gen_flag FUV webroot/img/flags/Flag_of_Nigeria.svg shrunken
gen_flag HAU webroot/img/flags/Flag_of_Nigeria.svg shrunken
gen_flag IBO webroot/img/flags/Flag_of_Nigeria.svg shrunken
gen_flag URH webroot/img/flags/Flag_of_Nigeria.svg shrunken
gen_flag YOR webroot/img/flags/Flag_of_Nigeria.svg shrunken
gen_flag KMR webroot/img/flags/Flag_of_Kurdistan.svg centered
gen_flag SDH webroot/img/flags/Flag_of_Kurdistan.svg centered
gen_flag CKB webroot/img/flags/Flag_of_Kurdistan.svg centered
gen_flag DTP webroot/img/flags/Flag_of_Malaysia.svg
gen_flag KXI webroot/img/flags/Flag_of_Malaysia.svg
gen_flag KZJ webroot/img/flags/Flag_of_Malaysia.svg
gen_flag MVV webroot/img/flags/Flag_of_Malaysia.svg
gen_flag TMW webroot/img/flags/Flag_of_Malaysia.svg
gen_flag ZLM webroot/img/flags/Flag_of_Malaysia.svg
gen_flag SYL webroot/img/flags/benbase.svg squared
gen_flag AYM webroot/img/flags/Wiphala.svg squared
gen_flag QUE webroot/img/flags/Wiphala.svg squared
gen_flag NNO webroot/img/flags/Flag_of_Norway.svg
gen_flag NOB webroot/img/flags/Flag_of_Norway.svg
gen_flag ENM webroot/img/flags/Flag_of_England.svg squared
gen_flag LIJ webroot/img/flags/Flag_of_England.svg squared
gen_flag NGT webroot/img/flags/lao.svg centered
gen_flag HNJ webroot/img/flags/lao.svg centered

# Flags consisting of ISO code only
gen_flag GUW
gen_flag IGS
gen_flag KAS
gen_flag KNC
gen_flag NJO
gen_flag NNB
gen_flag NST
gen_flag SHY
gen_flag SWC
gen_flag TUM
