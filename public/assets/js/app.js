/* Helper bersama: fetch JSON, escape, lookup dropdown, toast */
window.App = (function () {
  const BASE = window.BASE_URL || '';
  const csrf = () => (document.querySelector('meta[name="csrf-token"]') || {}).content || '';

  const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

  const today = () => new Date(Date.now() - new Date().getTimezoneOffset() * 60000).toISOString().slice(0, 10);

  async function api(url, opt = {}) {
    opt.headers = Object.assign({ 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf() }, opt.headers || {});
    const res = await fetch(url, opt);
    if (res.status === 401) { location.href = BASE + '/login'; return { ok: false, status: 401, json: {} }; }
    const json = await res.json().catch(() => ({ message: 'Respon server tidak valid.' }));
    return { ok: res.ok, status: res.status, json };
  }

  async function lookup(name) {
    const r = await api(BASE + '/lookup/' + name);
    return r.ok ? r.json : [];
  }

  const toast = (icon, title) => Swal.fire({ toast: true, position: 'top-end', icon, title, showConfirmButton: false, timer: 2500 });

  return { BASE, api, esc, lookup, toast, today };
})();
