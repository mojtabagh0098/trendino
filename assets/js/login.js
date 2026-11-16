document.addEventListener('DOMContentLoaded', () => {
  const root = document.querySelector('#auth'); if (!root) return;
  const $ = s => root.querySelector(s), all = s => [...root.querySelectorAll(s)];
  const notice = $('.notice'), phoneForm = $('#phone-form'), passwordForm = $('#password-form'), otpForm = $('#otp-form'), newForm = $('#new-password-form');
  let phone = '', purpose = root.dataset.forgot === '1' ? 'reset' : '', timer = null;
  const digits = value => value.replace(/[۰-۹٠-٩]/g, c => String('۰۱۲۳۴۵۶۷۸۹٠١٢٣٤٥٦٧٨٩'.indexOf(c) % 10));
  const showNotice = msg => { notice.textContent = msg; notice.hidden = !msg; };
  const show = step => { all('[data-step]').forEach(el => el.hidden = el.dataset.step !== step); showNotice(''); all('.invalid').forEach(el => el.classList.remove('invalid')); };
  if (purpose === 'reset') { $('#phone-subtitle').textContent = 'برای تغییر رمز عبور شماره موبایل خود را وارد کنید'; $('.terms').hidden = true; }
  async function request(step, data = {}) {
    const body = new URLSearchParams({ action:'tno_auth', nonce:root.dataset.nonce, step, redirect_to:root.dataset.redirect, ...data });
    try { const response = await fetch(root.dataset.ajax, { method:'POST', credentials:'same-origin', body }); const result = await response.json(); if (!result.success) throw new Error(result.data?.message || 'خطایی رخ داد. دوباره تلاش کنید.'); return result.data || {}; }
    catch (error) { showNotice(error.message || 'ارتباط با سرور برقرار نشد.'); throw error; }
  }
  async function run(button, fn) { if (button.dataset.busy) return; button.dataset.busy = '1'; button.disabled = true; try { await fn(); } catch (_) {} finally { delete button.dataset.busy; update(); } }
  function update() {
    phoneForm.querySelector('button').disabled = !phoneForm.phone.value.trim();
    passwordForm.querySelector('.primary').disabled = !passwordForm.password.value;
    otpForm.querySelector('.primary').disabled = all('.otp-row input').some(i => !i.value);
    newForm.querySelector('.primary').disabled = score(newForm.password.value) !== 4 || !newForm.confirm.value || newForm.password.value !== newForm.confirm.value;
  }
  function countdown() { let remaining = Number(root.dataset.cooldown) || 94; clearInterval(timer); const target = $('.timer'); function tick() { if (remaining <= 0) { clearInterval(timer); target.innerHTML = '<button type="button" id="resend">دریافت مجدد کد</button>'; return; } target.innerHTML = '<b>' + String(Math.floor(remaining / 60)).padStart(2,'0') + ':' + String(remaining % 60).padStart(2,'0') + '</b> دقیقه مانده تا دریافت مجدد کد'; remaining--; } tick(); timer = setInterval(tick, 1000); }
  function otpScreen() { $('#target-phone').textContent = phone; all('.otp-row input').forEach(i => i.value = ''); $('#back-password').hidden = purpose !== 'login'; show('otp'); countdown(); update(); all('.otp-row input')[0].focus(); }
  phoneForm.addEventListener('submit', e => { e.preventDefault(); run(phoneForm.querySelector('button'), async () => { phone = digits(phoneForm.phone.value).replace(/[\s\-()]/g,''); const result = await request('start', { phone, forgot: purpose === 'reset' ? '1' : '' }); phone = result.phone; if (result.step === 'password') { purpose = 'login'; show('password'); } else { if (purpose !== 'reset') purpose = 'register'; otpScreen(); } }); });
  passwordForm.addEventListener('submit', e => { e.preventDefault(); run(passwordForm.querySelector('.primary'), async () => { try { const result = await request('password', {password:passwordForm.password.value}); location.assign(result.redirect); } catch (err) { passwordForm.password.classList.add('invalid'); throw err; } }); });
  $('#otp-login').addEventListener('click', e => run(e.currentTarget, async () => { await request('send'); otpScreen(); }));
  $('#forgot').addEventListener('click', () => { purpose = 'reset'; $('#phone-subtitle').textContent = 'برای تغییر رمز عبور شماره موبایل خود را وارد کنید'; $('.terms').hidden = true; show('phone'); });
  $('#back-password').addEventListener('click', () => show('password'));
  $('.back-phone').addEventListener('click', () => show('phone'));
  $('.timer').addEventListener('click', e => { if (e.target.id === 'resend') run(e.target, async () => { await request('send'); countdown(); }); });
  const inputs = all('.otp-row input');
  inputs.forEach((input, index) => { input.addEventListener('input', () => { input.value = digits(input.value).replace(/\D/g,'').slice(-1); $('.otp-row').classList.remove('invalid'); if (input.value && inputs[index+1]) inputs[index+1].focus(); update(); }); input.addEventListener('keydown', e => { if (e.key === 'Backspace' && !input.value && inputs[index-1]) inputs[index-1].focus(); }); input.addEventListener('paste', e => { const value = digits(e.clipboardData.getData('text')).replace(/\D/g,'').slice(0,5); if (value.length > 1) { e.preventDefault(); value.split('').forEach((v,i) => inputs[i].value=v); inputs[Math.min(value.length,4)].focus(); update(); } }); });
  otpForm.addEventListener('submit', e => { e.preventDefault(); run(otpForm.querySelector('.primary'), async () => { try { const result = await request('verify', { code:inputs.map(i=>i.value).join('') }); if (result.redirect) location.assign(result.redirect); else show('newPassword'); } catch (err) { $('.otp-row').classList.add('invalid'); throw err; } }); });
  function score(value) { return Number(value.length>=8)+Number(/[0-9]/.test(value))+Number(/[a-z]/.test(value)&&/[A-Z]/.test(value))+Number(/[@#$%^&*!?]/.test(value)); }
  newForm.password.addEventListener('input', () => { const s = score(newForm.password.value), level = !newForm.password.value ? '' : s === 4 ? 'strong' : s >= 2 ? 'medium' : 'weak'; $('.strength').dataset.level = level; $('.strength-label').textContent = ({strong:'قوی',medium:'متوسط',weak:'ضعیف'})[level] || ''; });
  newForm.addEventListener('submit', e => { e.preventDefault(); run(newForm.querySelector('.primary'), async () => { const result = await request('set_password', {password:newForm.password.value, confirm:newForm.confirm.value}); location.assign(result.redirect); }); });
  all('.eye').forEach(button => button.addEventListener('click', () => { const input = button.parentElement.querySelector('input'); input.type = input.type === 'password' ? 'text' : 'password'; button.setAttribute('aria-label', input.type === 'password' ? 'نمایش رمز عبور' : 'پنهان کردن رمز عبور'); }));
  root.addEventListener('input', update); update();
});
