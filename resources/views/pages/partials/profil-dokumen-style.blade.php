{{--
    Gaya bersama untuk halaman profil:
    Sejarah, Visi & Misi, Pembelajaran, dan Fasilitas.
--}}
<style>
    .pd-page {
        --green: #17402f;
        --green-2: #1d4d3a;
        --green-3: #25684d;
        --gold: #b7791f;
        --gold-soft: #fdf4e0;
        --gold-light: #f3d9a0;
        --paper: #fbfaf6;
        --cream: #f3f1ea;
        --ink: #1f2a24;
        --muted: #66716b;
        --line: #e4e0d4;
        color: var(--ink);
        display: flex;
        flex-direction: column;
    }
    .pd-page *, .pd-page *::before, .pd-page *::after { box-sizing: border-box; }
    .pd-wrap { width: min(1100px, 92%); margin-inline: auto; }
    .pd-narrow { width: min(900px, 92%); margin-inline: auto; }
    .pd-page section[id] { scroll-margin-top: 90px; }

    /* ====== Hero ====== */
    .pd-hero {
        position: relative;
        overflow: hidden;
        padding: 38px 0 58px;
        color: #fff;
        text-align: center;
        background: linear-gradient(135deg, var(--green) 0%, var(--green-2) 68%, #2a6a4d 100%);
    }
    .pd-hero::before {
        content: "";
        position: absolute;
        inset: 0;
        background-image: radial-gradient(rgba(255, 255, 255, .12) 1px, transparent 1px);
        background-size: 22px 22px;
        opacity: .55;
        -webkit-mask-image: radial-gradient(ellipse at center, #000, transparent 78%);
        mask-image: radial-gradient(ellipse at center, #000, transparent 78%);
    }
    .pd-hero::after {
        content: "";
        position: absolute;
        left: -150px;
        top: -170px;
        width: 440px;
        height: 440px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(243, 217, 160, .16), transparent 70%);
    }
    .pd-hero .pd-wrap { position: relative; z-index: 1; }
    .pd-crumb { display: flex; flex-wrap: wrap; align-items: center; justify-content: center; gap: 8px; margin-bottom: 26px; font-size: 13px; color: rgba(255, 255, 255, .7); }
    .pd-crumb a { color: #fff; text-decoration: none; }
    .pd-crumb a:hover { color: var(--gold-light); }
    .pd-crumb i { font-size: 9px; opacity: .6; }
    .pd-hero-inner { max-width: 900px; margin-inline: auto; }

    .pd-eyebrow { display: inline-flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 14px; font-size: 11.5px; font-weight: 700; letter-spacing: .16em; text-transform: uppercase; color: var(--gold); }
    .pd-eyebrow::before { content: ""; width: 26px; height: 2px; background: currentColor; }
    .pd-eyebrow.light { color: var(--gold-light); }

    .pd-hero h1 { margin: 0 auto 16px; font-size: clamp(1.75rem, 4vw, 2.9rem); font-weight: 800; line-height: 1.18; letter-spacing: -.02em; color: #fff; }
    .pd-hero h1 span { color: var(--gold-light); }
    .pd-hero p { margin: 0 auto 22px; max-width: 64ch; font-size: 1rem; line-height: 1.8; color: rgba(255, 255, 255, .85); }

    .pd-hero-meta { display: inline-flex; flex-wrap: wrap; align-items: center; justify-content: center; gap: 10px; margin: 0 0 22px; padding: 8px 16px; border-radius: 999px; border: 1px solid rgba(255, 255, 255, .2); background: rgba(255, 255, 255, .08); font-size: .82rem; color: rgba(255, 255, 255, .82); }
    .pd-hero-meta b { font-size: 1.1rem; font-weight: 800; color: var(--gold-light); }

    .pd-nav { display: flex; flex-wrap: wrap; justify-content: center; gap: 8px; }
    .pd-nav a { display: inline-flex; align-items: center; gap: 7px; padding: 8px 16px; border-radius: 999px; font-size: .86rem; font-weight: 600; text-decoration: none; color: #fff; background: rgba(255, 255, 255, .08); border: 1px solid rgba(255, 255, 255, .22); transition: all .2s ease; }
    .pd-nav a:hover { background: #fff; color: var(--green); }
    .pd-nav a i { font-size: 11px; }

    /* ====== Section umum ====== */
    .pd-section-wrap { padding: 60px 0; }
    .pd-doc-area { padding: 64px 0; background: linear-gradient(180deg, #f7f5ef 0%, #fbfaf6 45%, #f7f5ef 100%); }
    .pd-doc-area .pd-doc { max-width: none; }
    .pd-alt { background: var(--cream); }
    .pd-head { max-width: 680px; margin: 0 0 30px; }
    .pd-head h2 { margin: 0 0 10px; font-size: clamp(1.5rem, 2.6vw, 2.05rem); font-weight: 800; line-height: 1.3; letter-spacing: -.015em; color: var(--green); }
    .pd-head p { margin: 0; font-size: .98rem; line-height: 1.75; color: var(--muted); }

    /* ====== Dokumen / artikel ====== */
    .pd-doc {
        max-width: 900px;
        margin-inline: auto;
        padding: clamp(24px, 4vw, 46px);
        border-radius: 22px;
        border: 1px solid var(--line);
        background: #fff;
        box-shadow: 0 22px 50px -34px rgba(23, 64, 47, .35);
    }
    .pd-doc-figure { margin: 0 0 18px; }
    .pd-doc-figure img { display: block; width: 100%; max-width: 720px; max-height: 520px; margin-inline: auto; object-fit: cover; border-radius: 16px; border: 1px solid var(--line); }
    .pd-doc-figure figcaption { margin-top: 12px; text-align: center; font-size: .84rem; line-height: 1.6; color: var(--muted); }
    .pd-doc-figure figcaption::before { content: "— "; }

    .pd-doc-placeholder { display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 10px; min-height: 220px; border: 1px dashed var(--line); border-radius: 16px; background: linear-gradient(160deg, #fff, var(--cream)); text-align: center; }
    .pd-doc-placeholder img { width: 72px; height: 72px; object-fit: contain; opacity: .9; }
    .pd-doc-placeholder span { max-width: 40ch; font-size: .82rem; line-height: 1.6; color: var(--muted); }

    .pd-chapter { margin: 0; padding: 34px 0 0; border-top: 1px solid var(--line); }
    .pd-chapter + .pd-chapter { margin-top: 36px; }
    .pd-chapter:first-child { padding-top: 0; border-top: 0; }
    .pd-chapter-title { display: flex; align-items: flex-start; gap: 12px; margin: 0 0 22px; font-size: clamp(1.12rem, 2vw, 1.4rem); font-weight: 800; line-height: 1.42; letter-spacing: -.01em; color: var(--green); }
    .pd-chapter-title::before { content: ""; flex: 0 0 auto; width: 4px; height: 1.15em; margin-top: .12em; border-radius: 2px; background: var(--gold); }

    .pd-section { margin: 0 0 24px; }
    .pd-section:last-child { margin-bottom: 0; }
    .pd-section-title { margin: 0 0 8px; font-size: 1.02rem; font-weight: 700; line-height: 1.5; color: var(--green-2); }
    .pd-prose { font-size: 1rem; line-height: 1.9; color: #3d4a43; }
    .pd-prose p { margin: 0 0 12px; }
    .pd-prose p:last-child { margin-bottom: 0; }

    /* ====== Kartu fitur (misi, nilai, fasilitas) ====== */
    .pd-feature-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 14px; }
    .pd-feature { padding: 22px; border-radius: 18px; border: 1px solid var(--line); background: #fff; transition: transform .25s ease, box-shadow .25s ease; }
    .pd-feature:hover { transform: translateY(-3px); box-shadow: 0 14px 30px rgba(23, 64, 47, .08); }
    .pd-feature .ico { display: flex; align-items: center; justify-content: center; width: 44px; height: 44px; margin-bottom: 14px; border-radius: 12px; background: var(--gold-soft); color: var(--gold); font-size: 17px; }
    .pd-feature h3 { margin: 0 0 6px; font-size: 1rem; line-height: 1.4; color: var(--green); }
    .pd-feature p { margin: 0; font-size: .88rem; line-height: 1.7; color: var(--muted); }

    .pd-numbered { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 14px; }
    .pd-numbered-item { display: flex; gap: 14px; padding: 20px; border-radius: 16px; border: 1px solid var(--line); background: #fff; transition: transform .25s ease, box-shadow .25s ease; }
    .pd-numbered-item:hover { transform: translateY(-3px); box-shadow: 0 14px 30px rgba(23, 64, 47, .08); }
    .pd-numbered-item .num { display: flex; flex: 0 0 auto; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 50%; background: var(--green); color: #fff; font-size: .82rem; font-weight: 800; }
    .pd-numbered-item h3 { margin: 4px 0 5px; font-size: .98rem; line-height: 1.4; color: var(--green); }
    .pd-numbered-item p { margin: 0; font-size: .87rem; line-height: 1.7; color: var(--muted); }

    /* ====== Visi ====== */
    .pd-visi-wrap { display: grid; grid-template-columns: minmax(0, .85fr) minmax(0, 1.15fr); gap: 44px; align-items: center; }
    .pd-visi-copy h2 { margin: 0 0 10px; font-size: clamp(1.5rem, 2.6vw, 2.05rem); font-weight: 800; line-height: 1.3; letter-spacing: -.015em; color: var(--green); }
    .pd-visi-copy p { margin: 0; max-width: 46ch; font-size: .98rem; line-height: 1.8; color: var(--muted); }
    .pd-visi-card { position: relative; overflow: hidden; padding: 34px 36px; border-radius: 22px; color: #fff; background: linear-gradient(135deg, var(--green), var(--green-3)); box-shadow: 0 20px 44px -20px rgba(20, 83, 45, .5); }
    .pd-visi-card::after { content: ""; position: absolute; right: -70px; bottom: -100px; width: 260px; height: 260px; border-radius: 50%; border: 40px solid rgba(255, 255, 255, .05); }
    .pd-visi-card > * { position: relative; z-index: 1; }
    .pd-visi-card i { display: block; margin-bottom: 14px; font-size: 1.6rem; color: var(--gold-light); opacity: .8; }
    .pd-visi-card p { margin: 0; font-size: clamp(1.08rem, 2vw, 1.32rem); font-weight: 600; line-height: 1.7; color: #fff; }

    /* ====== Fasilitas ====== */
    .pd-facilities { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 18px; }
    .pd-facility { overflow: hidden; border-radius: 20px; border: 1px solid var(--line); background: #fff; box-shadow: 0 16px 34px -28px rgba(23, 64, 47, .4); transition: transform .25s ease, box-shadow .25s ease; }
    .pd-facility:hover { transform: translateY(-4px); box-shadow: 0 20px 40px -24px rgba(23, 64, 47, .35); }
    .pd-facility-media { position: relative; aspect-ratio: 4 / 3; background: var(--cream); }
    .pd-facility-media img { display: block; width: 100%; height: 100%; object-fit: cover; }
    .pd-facility-ph { display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 10px; width: 100%; height: 100%; color: var(--gold); background: linear-gradient(160deg, #fff, var(--cream)); }
    .pd-facility-ph i { font-size: 2rem; }
    .pd-facility-ph span { font-size: .72rem; font-weight: 600; letter-spacing: .06em; text-transform: uppercase; color: var(--muted); }
    .pd-facility-body { padding: 18px 20px 20px; }
    .pd-facility-body h2 { margin: 0 0 6px; font-size: 1rem; line-height: 1.4; color: var(--green); }
    .pd-facility-body p { margin: 0; font-size: .86rem; line-height: 1.7; color: var(--muted); }

    /* ====== Motto ====== */
    .pd-motto { padding: 60px 0; color: #fff; text-align: center; background: linear-gradient(135deg, var(--green) 0%, var(--green-2) 100%); }
    .pd-motto .pd-wrap { max-width: 780px; }
    .pd-motto h2 { margin: 0; font-size: clamp(1.7rem, 3.6vw, 2.6rem); font-weight: 800; line-height: 1.35; color: #fff; }
    .pd-motto h2 span { color: var(--gold-light); }

    /* ====== Animasi ====== */
    .pd-rv { opacity: 0; transform: translateY(14px); transition: opacity .6s ease, transform .6s cubic-bezier(.2, .7, .2, 1); }
    .pd-rv.in { opacity: 1; transform: none; }
    @media (prefers-reduced-motion: reduce) {
        .pd-rv { opacity: 1 !important; transform: none !important; transition: none !important; }
    }

    /* ====== Responsif ====== */
    @media (max-width: 1040px) {
        .pd-facilities { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 960px) {
        .pd-visi-wrap { grid-template-columns: 1fr; gap: 26px; }
        .pd-visi-copy p { max-width: none; }
    }
    @media (max-width: 768px) {
        .pd-hero { padding: 30px 0 46px; }
        .pd-section-wrap, .pd-motto { padding: 46px 0; }
        .pd-doc { border-radius: 18px; }
        .pd-visi-card { padding: 26px 24px; border-radius: 18px; }
        .pd-chapter-title { gap: 10px; }
    }
    @media (max-width: 560px) {
        .pd-facilities { grid-template-columns: 1fr; }
        .pd-doc-figure img { border-radius: 12px; }
    }
</style>
