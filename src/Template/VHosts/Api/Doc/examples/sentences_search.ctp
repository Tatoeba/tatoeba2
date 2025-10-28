<?php
$this->set('version', 'sentences search');

$searchurl = '/v1#?route=get-/v1/sentences';
$this->assign('navlinks', '<li>' . $this->Html->Link('Sentences search endpoint', $searchurl) . '</li>');

$this->append('script');
?>

<script>
  document.addEventListener("DOMContentLoaded", init);

  function init() {
    const form = document.querySelector("form");
    form.addEventListener("submit", (event) => {
      event.preventDefault();
      const keywords = document.querySelector("#keywords");
      const lang = document.querySelector("#lang");
      const trans = document.querySelector("#trans");
      var params = 'q=' + encodeURIComponent(keywords.value)
                   + '&lang=' + encodeURIComponent(lang.value)
                   + '&sort=relevance';
      callSearchApi('/v1/sentences?' + params);
      //callSearchApi('https://api.tatoeba.org/v1/sentences?' + params);
    });
  }

  function callSearchApi(url) {
    const div = document.querySelector('#results');
    div.innerHTML = 'Contacting ' + url + ' ...';

    const req = new XMLHttpRequest();
    req.addEventListener("load", searchHandler);
    req.open('GET', url);
    req.send();
  }

  function searchHandler() {
    const resultsContainer = document.querySelector('#results');
    resultsContainer.innerHTML = '';

    if (this.status == 200) {
      const results = JSON.parse(this.responseText);
      appendResults(resultsContainer, results.data);
      appendPaging(resultsContainer, results.paging);
    } else {
      const error = JSON.parse(this.responseText);
      resultsContainer.innerHTML = 'Error: ' + error.message;
    }
  }

  function appendResults(elem, results) {
    if (results.length) {
      var list = document.createElement('ul');
      elem.appendChild(list);

      results.forEach((result) => {
        var li = document.createElement('li');
        li.innerText = result.text;
        list.appendChild(li);
      });
    } else {
      elem.innerHTML += 'No sentences found.';
    }
  }

  function appendPaging(elem, paging) {
    if (paging.first) {
      appendPagingButton(elem, '<< First page', paging.first);
    }
    if (paging.next) {
      appendPagingButton(elem, 'Next page >', paging.next);
    }
  }

  function appendPagingButton(elem, label, url) {
    var btn = document.createElement('button');
    btn.innerText = label;
    btn.addEventListener('click', () => {
      callSearchApi(url);
    });

    elem.appendChild(btn);
  }
</script>
<?php $this->end(); ?>

<main style="padding: 20px">
  <h2>Simple sentence search</h2>
  <p>Tip: use <code>Ctrl+U</code> or similar to see the source code of this page.</p>
  <h3>Search</h3>
  <div id="search">
    <form>
      Keywords <input id="keywords" name="keywords" /> in
      <input id="lang" name="lang" value="eng" style="width: 3em" />
      <input type="submit" value="Search sentences" />
    </form>
  </div>

  <h3>Results</h3>
  <div id="results">
  </div>
</main>
