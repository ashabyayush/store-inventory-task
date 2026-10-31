async function loadCustomers() {
  const s = $('#search').value.trim();
  const list = await api('/customers' + (s ? '?search=' + encodeURIComponent(s) : ''));

  $('#custTable').innerHTML = `<thead class="table-light"><tr>
      <th>Name</th><th>Email</th><th class="text-end">Orders</th>
      <th class="text-end">Total spent</th><th></th></tr></thead><tbody>` +
    (list.length ? list.map((c) => `<tr>
      <td>${esc(c.name)}</td>
      <td>${esc(c.email)}</td>
      <td class="text-end">${c.orders_count}</td>
      <td class="text-end">${cur(c.total_spent ?? 0)}</td>
      <td class="text-end">
        <a class="btn btn-sm btn-outline-primary" href="/history?email=${encodeURIComponent(c.email)}">
          View orders</a>
      </td></tr>`).join('')
      : '<tr><td colspan="5" class="text-muted">No customers found.</td></tr>') +
    '</tbody>';
}

let timer;
$('#search').addEventListener('input', () => {
  clearTimeout(timer);
  timer = setTimeout(loadCustomers, 300);
});

loadCustomers();