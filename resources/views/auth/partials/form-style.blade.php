<style>
    /* ===== Gaya halaman pemulihan kata sandi (kartu tunggal, beda dari halaman login) ===== */
    .pp-page {
        --green: #17402f; --cream: #f1eee6; --paper: #fbfaf6; --gold: #b7791f;
        --ink: #1f2a24; --muted: #6b756f; --line: #e4e0d4; --emerald: #12b886; --emerald-d: #0e9f73;
        --emerald-l: #2fe0a0; --shadow-lg: 0 26px 60px rgba(23, 64, 47, .16);

        position: relative; overflow: hidden; min-height: 100vh; min-height: 100dvh;
        display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 18px;
        padding: 40px 18px; font-family: 'Poppins', system-ui, -apple-system, sans-serif; color: var(--ink);
        background:
            radial-gradient(circle at 12% 6%, rgba(47, 224, 160, .18), transparent 38%),
            radial-gradient(circle at 90% 94%, rgba(183, 121, 31, .16), transparent 42%),
            linear-gradient(160deg, #f7f4ea 0%, #eef5f0 55%, #e7f1ec 100%);
    }
    .pp-page::before {
        content: ""; position: absolute; inset: 0; pointer-events: none;
        background-image: radial-gradient(rgba(23, 64, 47, .07) 1px, transparent 1px);
        background-size: 22px 22px;
        -webkit-mask-image: radial-gradient(circle at 50% 42%, #000, transparent 76%);
        mask-image: radial-gradient(circle at 50% 42%, #000, transparent 76%);
    }
    .pp-page *, .pp-page *::before, .pp-page *::after { box-sizing: border-box; }
    .pp-page a { text-decoration: none; color: inherit; }

    /* ---------- Identitas pesantren ---------- */
    .pp-brand { position: relative; z-index: 1; display: inline-flex; align-items: center; gap: 11px; }
    .pp-brand-img {
        width: 42px; height: 42px; border-radius: 13px; background: #fff; padding: 6px; flex: none;
        border: 1px solid var(--line); box-shadow: 0 8px 18px rgba(23, 64, 47, .1); transition: transform .25s ease;
    }
    .pp-brand:hover .pp-brand-img { transform: scale(1.06); }
    .pp-brand-img img { width: 100%; height: 100%; object-fit: contain; display: block; }
    .pp-brand small { display: block; font-size: 10.5px; font-weight: 500; color: var(--muted); line-height: 1.15; }
    .pp-brand strong { display: block; font-size: 15.5px; font-weight: 600; color: var(--green); line-height: 1.3; }

    /* ---------- Kartu utama ---------- */
    .pp-card {
        position: relative; z-index: 1; width: 100%; max-width: 472px; background: #fff;
        border: 1px solid rgba(228, 224, 212, .9); border-radius: 26px; overflow: hidden;
        padding: 34px 32px 26px; box-shadow: var(--shadow-lg); animation: ppin .5s ease both;
    }
    .pp-card::before {
        content: ""; position: absolute; top: 0; left: 0; right: 0; height: 5px;
        background: linear-gradient(90deg, var(--emerald) 0%, var(--emerald-l) 45%, var(--gold) 100%);
    }
    @keyframes ppin {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .pp-icon {
        width: 54px; height: 54px; border-radius: 18px; display: grid; place-items: center; font-size: 21px;
        color: var(--emerald-d); background: linear-gradient(135deg, #e6f7ef, #d2efe2);
        border: 1px solid #c7e9da; box-shadow: 0 10px 22px rgba(18, 184, 134, .16); margin-bottom: 16px;
    }
    .pp-eyebrow { display: inline-block; font-size: 10.5px; font-weight: 600; letter-spacing: .14em; text-transform: uppercase; color: var(--gold); }
    .pp-title { font-size: 25px; font-weight: 600; color: var(--green); margin: 6px 0 0; line-height: 1.28; }
    .pp-title span { color: var(--emerald-d); }
    .pp-sub { font-size: 13px; color: var(--muted); line-height: 1.75; margin: 8px 0 20px; }

    .pp-alert {
        display: flex; align-items: flex-start; gap: 10px; font-size: 12.5px; font-weight: 500; line-height: 1.6;
        background: #eaf7f1; border: 1px solid #bfe8d6; color: #0b6b4d; padding: 12px 14px; border-radius: 14px; margin-bottom: 18px;
    }
    .pp-alert i { margin-top: 3px; color: var(--emerald-d); }
    .pp-alert.error { background: #fdf1f1; border-color: #f6c6c7; color: #b42318; }
    .pp-alert.error i { color: #d92d33; }

    /* ---------- Formulir ---------- */
    .pp-field { margin-bottom: 15px; }
    .pp-label { display: flex; align-items: center; justify-content: space-between; margin-bottom: 7px; }
    .pp-label label { font-size: 12px; font-weight: 600; color: var(--green); }

    .pp-input { position: relative; }
    .pp-input > i.lead {
        position: absolute; left: 15px; top: 50%; transform: translateY(-50%); font-size: 14px; color: #9aa59f;
        pointer-events: none; transition: color .2s ease;
    }
    .pp-input input {
        width: 100%; height: 50px; font-family: inherit; font-size: 14px; color: var(--ink); background: #fff;
        border: 1.5px solid var(--line); border-radius: 12px; padding: 0 16px 0 42px; outline: none;
        transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
    }
    .pp-input input::placeholder { color: #a9b2ad; }
    .pp-input input:focus { border-color: var(--emerald); box-shadow: 0 0 0 4px rgba(18, 184, 134, .12); }
    .pp-input:focus-within > i.lead { color: var(--emerald-d); }
    .pp-input input.invalid { border-color: #e5484d; background: #fff7f7; }
    .pp-input input.invalid:focus { box-shadow: 0 0 0 4px rgba(229, 72, 77, .12); }
    .pp-input.has-eye input { padding-right: 48px; }
    .pp-eye {
        position: absolute; right: 7px; top: 50%; transform: translateY(-50%); width: 36px; height: 36px; border: 0;
        background: none; border-radius: 9px; cursor: pointer; color: #9aa59f; font-size: 14px; transition: all .2s ease;
    }
    .pp-eye:hover { background: var(--cream); color: var(--green); }

    .pp-error { display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 500; color: #d92d33; margin-top: 7px; }
    .pp-hint { display: flex; align-items: flex-start; gap: 6px; font-size: 11.5px; color: var(--muted); line-height: 1.6; margin-top: 7px; }
    .pp-hint i { margin-top: 3px; font-size: 10.5px; color: var(--gold); }

    .pp-btn {
        width: 100%; height: 52px; border: 0; cursor: pointer; font-family: inherit; font-size: 14px; font-weight: 600; color: #fff;
        background: linear-gradient(135deg, var(--emerald) 0%, var(--emerald-d) 100%); border-radius: 12px;
        display: inline-flex; align-items: center; justify-content: center; gap: 10px;
        box-shadow: 0 10px 24px rgba(18, 184, 134, .28); transition: all .25s ease; margin-top: 6px;
    }
    .pp-btn:hover { filter: brightness(.96); transform: translateY(-2px); }
    .pp-btn:active { transform: translateY(0); }
    .pp-btn i { font-size: 12px; }
    .pp-btn.loading { pointer-events: none; opacity: .85; }
    .pp-btn.loading i { display: none; }
    .pp-spin {
        display: none; width: 16px; height: 16px; border-radius: 50%; border: 2px solid rgba(255, 255, 255, .4);
        border-top-color: #fff; animation: ppspin .7s linear infinite;
    }
    .pp-btn.loading .pp-spin { display: inline-block; }
    @keyframes ppspin { to { transform: rotate(360deg); } }

    /* ---------- Langkah ringkas ---------- */
    .pp-steps {
        list-style: none; display: flex; align-items: flex-start; gap: 6px; margin: 22px 0 0; padding: 16px 0 0;
        border-top: 1px dashed var(--line);
    }
    .pp-steps li { flex: 1; text-align: center; font-size: 10.5px; line-height: 1.5; color: var(--muted); }
    .pp-steps .dot {
        width: 26px; height: 26px; border-radius: 50%; margin: 0 auto 6px; display: grid; place-items: center;
        font-size: 11px; font-weight: 600; color: var(--emerald-d); background: #e8f6ef; border: 1px solid #cdeade;
    }
    .pp-steps li.done .dot { background: var(--emerald); border-color: transparent; color: #fff; }
    .pp-steps li.active .dot { background: var(--green); border-color: transparent; color: #fff; box-shadow: 0 0 0 4px rgba(18, 184, 134, .14); }
    .pp-steps strong { display: block; color: var(--green); font-size: 11px; font-weight: 600; margin-bottom: 1px; }

    /* ---------- Tips & tautan bawah ---------- */
    .pp-tips {
        display: flex; align-items: flex-start; gap: 10px; margin-top: 16px; padding: 11px 13px;
        background: var(--paper); border: 1px solid var(--line); border-radius: 12px;
        font-size: 11.5px; color: var(--muted); line-height: 1.65;
    }
    .pp-tips i { margin-top: 2px; color: var(--gold); }

    .pp-foot { text-align: center; font-size: 12.5px; margin-top: 18px; }
    .pp-foot a { display: inline-flex; align-items: center; gap: 7px; font-weight: 600; color: var(--emerald-d); transition: color .2s ease; }
    .pp-foot a:hover { color: var(--green); }
    .pp-foot a i { font-size: 11px; }

    .pp-copy { position: relative; z-index: 1; font-size: 11px; color: #8a948e; text-align: center; }

    /* ---------- Responsif ---------- */
    @media (max-width: 480px) {
        .pp-page { padding: 26px 14px; gap: 14px; }
        .pp-card { padding: 26px 20px 22px; border-radius: 22px; }
        .pp-title { font-size: 22px; }
        .pp-icon { width: 48px; height: 48px; border-radius: 16px; font-size: 19px; }
        .pp-steps li { font-size: 9.5px; }
        .pp-steps strong { font-size: 10px; }
    }
</style>
