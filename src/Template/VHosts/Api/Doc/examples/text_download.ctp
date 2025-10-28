<?php
$this->set('pagetitle', 'sentences download');

$searchurl = $docurl + ['#' => '?route=get-/v1/sentences'];
$this->assign('navlinks', '<li>' . $this->Html->Link('Sentences search endpoint', $searchurl) . '</li>');

$this->append('script');
?>

<script>
  document.addEventListener("DOMContentLoaded", init);

  var chunks; // array of downloaded chunks
  var request, timeoutId; // used for cancel button
  var nbTotalSentences, nbDownloadedSentences;

  function init() {
    const form = document.querySelector("#start");
    form.addEventListener("submit", (event) => {
      event.preventDefault();
      const url = document.querySelector("#url");
      initDownload(url.value);
    });
  }

  function setStatus(text) {
    const div = document.querySelector('#status');
    div.innerHTML = text;
  }

  function initDownload(url) {
    chunks = ["\uFEFF"]; // Byte order mark
    nbTotalSentences = 0;
    nbDownloadedSentences = 0;
    setStatus('Contacting ' + url + ' ...');
    showCancelButton();
    startDownload(url);
  }

  function startDownload(url) {
    request = new XMLHttpRequest();
    request.addEventListener("load", downloadHandler);
    request.open('GET', url);
    request.send();
  }

  function showCancelButton() {
    const form = document.querySelector("#cancel");
    form.style.display = 'initial';
    form.addEventListener("submit", (event) => {
      event.preventDefault();
      request.abort();
      clearTimeout(timeoutId);
      setStatus('');
      form.style.display = 'none';
    });
  }

  function downloadHandler() {
    try {
      if (this.status == 200) {
          const results = JSON.parse(this.responseText);
          appendResults(results);
          updateCounter(results);
          downloadNextPage(results);
      } else {
        const error = JSON.parse(this.responseText);
        setStatus('Error: ' + error.message);
      }
    } catch (e) {
      setStatus('JavaScript error: ' + e);
    }
  }

  function updateCounter(results) {
    if (results.paging.total) {
      nbTotalSentences = results.paging.total;
    }
    nbDownloadedSentences += results.data.length;
    const perc = nbDownloadedSentences / nbTotalSentences * 100;
    setStatus('Downloading: ' + perc.toFixed(0) + '% done (' + nbDownloadedSentences + ' of ' + nbTotalSentences + ' sentences)');
  }

  function appendResults(results) {
    if (results.data.length) {
      const lines = results.data.map((sentence) => {
         return sentence.id + ' - ' + sentence.text + '\r\n';
      });
      chunks.push(lines.join(''));
    }
  }

  function downloadNextPage(results) {
    if (results.paging.has_next) {
      timeoutId = setTimeout(
        () => { startDownload(results.paging.next) },
        1000
      );
    } else {
      finishDownload();
    }
  }

  function finishDownload() {
    setStatus('Download completed (' + nbDownloadedSentences + ' sentences)');
    const a = document.createElement('a');
    a.href = URL.createObjectURL(new Blob(chunks));
    a.download = 'sentences.txt';
    a.click();
  }
</script>
<?php $this->end(); ?>

<main style="padding: 20px">
  <h2>Download sentences into a text file</h2>

  <h3>Instructions</h3>
  <h4>How to use</h4>
  <ol>
    <li>Input a <code>/v1/sentences</code> API URL below with your own filters.</li>
    <li>Click the button to initiate download.</li>
    <li>Sentences will be downloaded page by page.</li>
    <li>After all the pages are downloaded, the browser will save the sentences into a text file.</li>
  </ol>

  <h4>Tips</h4>
  <ul>
    <li>Use <code>Ctrl+U</code> or similar to see the source code of this page.</li>
    <li>Use <code>limit=100</code> parameter for a more efficient download.</li>
  </ul>

  <h3>Download</h3>
    <form id="start" style="display: flex; flex-wrap: wrap">
      API URL to fetch:
      <input id="url" name="url" value="https://api.tatoeba.org/v1/sentences" style="flex: 1" />
      <input id="start" type="submit" value="Download sentences as text" />
    </form>

  <h3>Status</h3>
  <div id="status">
  </div>
  <form id="cancel" style="display: none">
    <input id="cancel" type="submit" value="Cancel download" />
  </form>
</main>
