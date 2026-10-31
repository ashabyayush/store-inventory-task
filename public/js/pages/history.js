let page = 1;
let email = '';

function detailHtml(o) {
  const rows = o.items.map((i) => `<tr>
    <td>${esc(i.product.name)} <code>${esc(i.product.code)}</code></td>
    <td class="text-end">${i.quantity}</td>
    <td class="text-end">${cur(i.unit_price)}</td>
    <td class="text-end text-nowrap">${Number(i.tax_percent)}% · ${cur(i.tax_amount)}</td>
    <td class="text-end">${cur(i.line_total)}</td></tr>`).join('');

  return `<div class="table-responsive"><table class="table table-sm align-middle mb-0">
    <thead class="table-light"><tr>
      <th>Item</th><th class="text-end">Qty</th><th class="text-end">Unit price</th>
      <th class="text-end">Tax</th><th class="text-end">Line total</th></tr></thead>
    <tbody>${rows}</tbody>
    <tfoot><tr>
      <td colspan="4" class="text-muted">Subtotal ${cur(o.subtotal)} + tax ${cur(o.tax_total)}</td>
      <td class="text-end fw-bold">${cur(o.grand_total)}</td></tr></tfoot>
  </table></div>`;
}

function rowHtml(o) {
  const emailed = o.email_status === 'emailed';
  return `<tr class="order-row" data-id="${o.id}">
      <td class="fw-bold">${esc(o.order_number)}</td>
      <td>${esc(o.customer.name)}<div class="small text-muted">${esc(o.customer.email)}</div></td>
      <td class="text-nowrap">${new Date(o.created_at).toLocaleString()}</td>
      <td class="text-end">${o.items.length}</td>
      <td class="text-end fw-bold">${cur(o.grand_total)}</td>
      <td class="text-center"><span class="badge rounded-pill ${emailed ? 'bg-success' : 'bg-secondary'}">${emailed ? 'emailed' : 'email queued'}</span></td>
    </tr>
    <tr class="detail-row d-none" id="d-${o.id}"><td colspan="6">${detailHtml(o)}</td></tr>`;
}

async function load() {
  const qs = new URLSearchParams({ page });
  if (email) qs.set('email', email);

  try {
    const d = await api('/orders?' + qs.toString());

    $('#summary').textContent = email
      ? `${d.total} order(s) matching "${email}"`
      : `${d.total} order(s) in total`;

    $('#ordTable').innerHTML = `<thead class="table-light"><tr>
        <th>Order</th><th>Customer</th><th>Date</th>
        <th class="text-end">Items</th><th class="text-end">Total</th><th class="text-center">Email</th>
      </tr></thead><tbody>` +
      (d.data.length ? d.data.map(rowHtml).join('')
        : '<tr><td colspan="6" class="text-muted">No orders found.</td></tr>') +
      '</tbody>';

    document.querySelectorAll('.order-row').forEach((r) => (r.onclick = () =>
      $('#d-' + r.dataset.id).classList.toggle('d-none')));

    $('#pager').innerHTML = d.last_page > 1 ? `
      <button class="btn btn-sm btn-outline-secondary" id="prev" ${d.current_page <= 1 ? 'disabled' : ''}>← Prev</button>
      <span class="text-muted small">Page ${d.current_page} of ${d.last_page}</span>
      <button class="btn btn-sm btn-outline-secondary" id="next" ${d.current_page >= d.last_page ? 'disabled' : ''}>Next →</button>` : '';

    if ($('#prev')) $('#prev').onclick = () => { page--; load(); };
    if ($('#next')) $('#next').onclick = () => { page++; load(); };
  } catch (e) {
    $('#summary').innerHTML = alertBox('err', errText(e));
  }
}

function applyFilter() {
  email = $('#hemail').value.trim();
  page = 1;
  load();
}

$('#lookup').onclick = applyFilter;
$('#hemail').addEventListener('keydown', (e) => { if (e.key === 'Enter') applyFilter(); });
$('#clear').onclick = () => { $('#hemail').value = ''; applyFilter(); };

// Customers page-la irundhu /history?email=... nu vandha prefill
const q = new URLSearchParams(location.search).get('email');
if (q) { $('#hemail').value = q; email = q; }

load();