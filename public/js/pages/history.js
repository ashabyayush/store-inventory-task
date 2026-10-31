let page = 1;
let email = '';
let ORDERS = {};
let orderModal;

const emailBadge = (o) => {
  const emailed = o.email_status === 'emailed';
  return `<span class="badge rounded-pill ${emailed ? 'bg-success' : 'bg-secondary'}">${emailed ? 'emailed' : 'email queued'}</span>`;
};

function rowHtml(o) {
  return `<tr class="order-row" data-id="${o.id}" title="Click to view order details">
      <td class="fw-bold">${esc(o.order_number)}</td>
      <td>${esc(o.customer.name)}<div class="small text-muted">${esc(o.customer.email)}</div></td>
      <td class="text-nowrap">${new Date(o.created_at).toLocaleString()}</td>
      <td class="text-end">${o.items.length}</td>
      <td class="text-end fw-bold">${cur(o.grand_total)}</td>
      <td class="text-center">${emailBadge(o)}</td>
      <td class="text-end"><button type="button" class="btn btn-sm btn-outline-primary">View</button></td>
    </tr>`;
}

function openOrder(id) {
  const o = ORDERS[id];
  if (!o) return;

  const rows = o.items.map((i) => `<tr>
      <td>${esc(i.product.name)}<div><code>${esc(i.product.code)}</code></div></td>
      <td class="text-end">${i.quantity}</td>
      <td class="text-end">${cur(i.unit_price)}</td>
      <td class="text-end text-nowrap">${Number(i.tax_percent)}% · ${cur(i.tax_amount)}</td>
      <td class="text-end fw-semibold">${cur(i.line_total)}</td>
    </tr>`).join('');

  $('#omTitle').textContent = o.order_number;
  $('#omDate').textContent = new Date(o.created_at).toLocaleString();

  $('#omBody').innerHTML = `
    <div class="row g-3 mb-3">
      <div class="col-12 col-md-6">
        <div class="small text-muted">Customer</div>
        <div class="fw-bold">${esc(o.customer.name)}</div>
        <div class="small text-muted">${esc(o.customer.email)}</div>
      </div>
      <div class="col-6 col-md-3">
        <div class="small text-muted">Products</div>
        <div class="fw-bold">${o.items.length}</div>
      </div>
      <div class="col-6 col-md-3">
        <div class="small text-muted">Confirmation</div>
        <div>${emailBadge(o)}</div>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead class="table-light"><tr>
          <th>Product</th><th class="text-end">Qty</th><th class="text-end">Unit price</th>
          <th class="text-end">Tax</th><th class="text-end">Line total</th>
        </tr></thead>
        <tbody>${rows}</tbody>
        <tfoot>
          <tr><td colspan="4" class="text-end text-muted">Subtotal</td><td class="text-end">${cur(o.subtotal)}</td></tr>
          <tr><td colspan="4" class="text-end text-muted">Tax</td><td class="text-end">${cur(o.tax_total)}</td></tr>
          <tr><td colspan="4" class="text-end fw-bold">Grand total</td><td class="text-end fw-bold fs-5">${cur(o.grand_total)}</td></tr>
        </tfoot>
      </table>
    </div>`;

  orderModal.show();
}

async function load() {
  const qs = new URLSearchParams({ page });
  if (email) qs.set('email', email);

  try {
    const d = await api('/orders?' + qs.toString());

    ORDERS = {};
    d.data.forEach((o) => (ORDERS[o.id] = o));

    $('#summary').textContent = email
      ? `${d.total} order(s) matching "${email}"`
      : `${d.total} order(s) in total`;

    $('#ordTable').innerHTML = `<thead class="table-light"><tr>
        <th>Order</th><th>Customer</th><th>Date</th>
        <th class="text-end">Items</th><th class="text-end">Total</th>
        <th class="text-center">Email</th><th></th>
      </tr></thead><tbody>` +
      (d.data.length ? d.data.map(rowHtml).join('')
        : '<tr><td colspan="7" class="text-muted">No orders found.</td></tr>') +
      '</tbody>';

    document.querySelectorAll('.order-row').forEach((r) =>
      (r.onclick = () => openOrder(+r.dataset.id)));

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

orderModal = new bootstrap.Modal($('#orderModal'));
load();