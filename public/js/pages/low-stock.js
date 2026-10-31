async function loadLow() {
  const d = await api('/products/low-stock?threshold=' + $('#threshold').value);

  $('#lowTable').innerHTML = `<thead class="table-light"><tr>
      <th>Name</th><th>Code</th><th>Category</th><th class="text-end">Stock</th></tr></thead><tbody>` +
    (d.products.length ? d.products.map((p) => `<tr>
      <td>${esc(p.name)}</td><td><code>${esc(p.code)}</code></td>
      <td>${esc(p.category.name)}</td><td class="text-end low">${p.stock}</td></tr>`).join('')
      : '<tr><td colspan="4" class="text-muted">Nothing below the threshold 🎉</td></tr>') +
    '</tbody>';
}

$('#apply').onclick = loadLow;
loadLow();