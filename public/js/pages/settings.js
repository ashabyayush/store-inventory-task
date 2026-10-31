async function init() {
  const s = await api('/settings');

  $('#currency').innerHTML = Object.entries(s.currencies).map(([code, c]) =>
    `<option value="${code}" ${code === s.currency ? 'selected' : ''}>${code} (${esc(c.symbol)}) · ${esc(c.name)}</option>`
  ).join('');

  $('#aname').value = s.account.name;
  $('#aemail').value = s.account.email;
}

$('#saveCurrency').onclick = async () => {
  const box = $('#curMsg');
  box.innerHTML = '';
  try {
    const c = await api('/settings/currency', {
      method: 'PUT',
      body: JSON.stringify({ currency: $('#currency').value }),
    });
    box.innerHTML = alertBox('ok', `Currency set to ${c.name} (${c.symbol}).`);
    setTimeout(() => location.reload(), 700);
  } catch (e) {
    box.innerHTML = alertBox('err', errText(e));
  }
};

$('#saveAccount').onclick = async () => {
  const box = $('#accMsg');
  box.innerHTML = '';
  try {
    await api('/settings/account', {
      method: 'PUT',
      body: JSON.stringify({
        name: $('#aname').value,
        email: $('#aemail').value,
        password: $('#apass').value,
        password_confirmation: $('#apass2').value,
        current_password: $('#acurrent').value,
      }),
    });
    box.innerHTML = alertBox('ok', 'Login details updated.');
    ['#apass', '#apass2', '#acurrent'].forEach((s) => ($(s).value = ''));
    setTimeout(() => location.reload(), 900);
  } catch (e) {
    box.innerHTML = alertBox('err', errText(e));
  }
};

init();