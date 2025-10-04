<?php ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>ShortKenny</title>
  <style>
    body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial; margin: 40px auto; max-width: 760px; padding: 0 16px; }
    input[type=text]{ width:100%; padding:10px; margin:8px 0; }
    button{ padding:10px 16px; cursor:pointer; }
    table{ width:100%; border-collapse: collapse; margin-top:16px; }
    th, td{ border-bottom:1px solid #eee; padding:8px; text-align:left; }
  </style>
</head>
<body>
  <h1>ShortKenny</h1>
  <label for="url">Long URL</label>
  <input id="url" type="text" placeholder="https://example.com/very/long/url" />
  <button id="go">Shorten URL</button>
  <p id="result"></p>
  <h2>Recent links</h2>
  <table>
    <thead><tr><th>ID</th><th>Short</th><th>Clicks</th><th>Original</th></tr></thead>
    <tbody id="rows"></tbody>
  </table>
<script>
async function listLinks() {
  const res = await fetch('/api/links');
  const data = await res.json();
  const rows = document.getElementById('rows');
  rows.innerHTML = data.map(l => `
    <tr>
      <td>${l.id}</td>
      <td><a href="/${l.short_code}" target="_blank">${l.short_code}</a></td>
      <td>${l.click_count}</td>
      <td style="word-break:break-all">${l.original_url}</td>
    </tr>
  `).join('');
}
document.getElementById('go').onclick = async () => {
  const url = document.getElementById('url').value.trim();
  const r = await fetch('/api/links', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ original_url: url }) });
  const data = await r.json();
  if (data.error) { document.getElementById('result').textContent = data.error; return; }
  const shortUrl = location.origin + '/' + data.short_code;
  document.getElementById('result').innerHTML = 'Short URL: <a href="' + shortUrl + '" target="_blank">' + shortUrl + '</a>';
  document.getElementById('url').value = '';
  listLinks();
};
listLinks();
</script>
</body>
</html>
