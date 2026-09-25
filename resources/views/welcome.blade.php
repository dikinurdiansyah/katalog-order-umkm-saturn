<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>{{ config('app.name', 'Dapur Th Sant') }} — Wireframe & UI Specification Explorer</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600&display=swap">
<style>
:root{
  --bg:#f1f5f9;--surface:#ffffff;--ink:#0f172a;--mute:#64748b;--line:#e2e8f0;--stage:#e2e8f0;--stage-line:#cbd5e1;--chip:#f1f5f9;
  --teal:#B5451A;--teal-soft:#FDF3E8;--teal-ink:#8F3714;--indigo:#4f46e5;
  --font:"Plus Jakarta Sans","Segoe UI","Helvetica Neue",Arial,system-ui,-apple-system,"Noto Sans",sans-serif;
  --mono:"JetBrains Mono",ui-monospace,"SFMono-Regular",Menlo,Consolas,monospace;
  box-sizing:border-box;padding-top:env(safe-area-inset-top,0px);padding-bottom:env(safe-area-inset-bottom,0px);
}
@media (prefers-color-scheme: dark){
  :root:not([data-theme="light"]){--bg:#0b1120;--surface:#131b2e;--ink:#e8edf5;--mute:#94a3b8;--line:#25304a;--stage:#182238;--stage-line:#2a3757;--chip:#1c2740;--teal-soft:#2b1b15;--teal-ink:#fca5a5}
}
:root[data-theme="dark"]{--bg:#0b1120;--surface:#131b2e;--ink:#e8edf5;--mute:#94a3b8;--line:#25304a;--stage:#182238;--stage-line:#2a3757;--chip:#1c2740;--teal-soft:#2b1b15;--teal-ink:#fca5a5}
html{scroll-padding-top:calc(env(safe-area-inset-top,0px) + 20px);height:100%}
*{box-sizing:border-box}
[hidden]{display:none!important}
body{margin:0;background:var(--bg);color:var(--ink);font-family:var(--font);line-height:1.5;min-height:100%}
button,select{font:inherit;color:inherit}
:focus-visible{outline:2px solid #B5451A;outline-offset:2px}

/* ===== header ===== */
.top{background:#0f172a;color:#fff;padding:18px clamp(16px,3vw,32px);display:flex;gap:18px;flex-wrap:wrap;align-items:center;justify-content:space-between}
.top .l{display:flex;gap:16px;align-items:center;flex-wrap:wrap}
.sprint{background:#B5451A;color:#fff;border-radius:10px;padding:8px 14px;font-weight:800;font-size:14px;letter-spacing:.01em;white-space:nowrap}
.top h1{margin:0;font-size:clamp(18px,2.2vw,26px);line-height:1.2;letter-spacing:-.01em}
.top p{margin:2px 0 0;color:#cbd5e1;font-size:14px}
.top .r{display:flex;gap:10px;flex-wrap:wrap}
.pillb{all:unset;cursor:pointer;display:inline-flex;align-items:center;gap:8px;border:1px solid #3b231c;background:rgba(181,69,26,.15);color:#ff9e80;border-radius:99px;padding:10px 18px;font-weight:700;font-size:15px}
.pillb.w{color:#fff;background:rgba(255,255,255,.05);border-color:#26364d}
.pillb:hover{border-color:#ff9e80}
.tabsbar{background:#020617;padding:12px clamp(16px,3vw,24px);display:flex;gap:10px;align-items:center;overflow-x:auto;scrollbar-width:thin}
.tabsbar .lb{color:#94a3b8;font-size:12px;font-weight:700;letter-spacing:.08em;white-space:nowrap;margin-right:6px}
.tabsbar button{all:unset;cursor:pointer;white-space:nowrap;color:#cbd5e1;font-weight:600;font-size:15px;padding:10px 18px;border-radius:12px;display:inline-flex;gap:8px;align-items:center}
.tabsbar button:hover{background:#0f172a}
.tabsbar button[aria-selected="true"]{background:#B5451A;color:#fff}
.tabsbar button:focus-visible{outline:2px solid #ff9e80}

/* ===== main ===== */
.main{max-width:1900px;margin:0 auto;padding:22px clamp(12px,3vw,48px) 10px}
.info{background:var(--surface);border:1px solid var(--line);border-radius:22px;padding:18px 24px;display:flex;gap:18px;justify-content:space-between;align-items:flex-start;flex-wrap:wrap}
.info .t{flex:1 1 420px;min-width:0}
.trow{display:flex;gap:12px;align-items:center;flex-wrap:wrap}
.sbadge{background:var(--teal-soft);color:var(--teal-ink);border-radius:99px;padding:3px 13px;font-weight:700;font-size:13px;white-space:nowrap}
.info h2{margin:0;font-size:clamp(18px,2vw,24px);letter-spacing:-.01em}
.info p{margin:6px 0 0;color:var(--mute);font-size:15px}
.info p b{color:var(--ink)}
code{background:var(--teal-soft);color:var(--teal-ink);border-radius:6px;padding:1px 8px;font-family:var(--mono);font-size:13px}
.subs{display:flex;gap:6px;flex-wrap:wrap;margin-top:12px}
.subs button{all:unset;cursor:pointer;font-size:13px;font-weight:600;border:1px solid var(--line);color:var(--mute);border-radius:99px;padding:5px 13px;background:var(--surface)}
.subs button:hover{border-color:var(--teal)}
.subs button[aria-pressed="true"]{background:var(--ink);color:var(--bg);border-color:var(--ink)}
.dev{display:flex;gap:10px;flex-wrap:wrap;align-items:center}
.seg{display:inline-flex;background:var(--chip);border:1px solid var(--line);border-radius:14px;padding:5px;gap:4px}
.seg button{all:unset;cursor:pointer;padding:8px 15px;font-size:14px;font-weight:600;color:var(--mute);border-radius:10px;white-space:nowrap}
.seg button[aria-pressed="true"]{background:var(--surface);color:var(--ink);box-shadow:0 1px 3px rgba(15,23,42,.15)}
.seg.sm button{padding:6px 11px;font-size:13px}
.stagebox{margin-top:22px;background:var(--stage);border:1px solid var(--stage-line);border-radius:36px;padding:clamp(12px,2.6vw,38px)}
.stagein{display:flex;justify-content:center}
.dscale{width:100%;overflow:hidden}
.mwrap{max-width:100%;overflow-x:auto;padding:4px}
.notes{margin-top:18px;background:var(--surface);border:1px solid var(--line);border-radius:22px;padding:18px 24px}
.notes h3{margin:0 0 12px;font-size:16px}
.ng{display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:12px 26px}
.ni{font-size:14px;color:var(--mute)}
.ni b{color:var(--ink)}
.ptag{display:inline-block;background:var(--indigo);color:#fff;font-family:var(--mono);font-size:11px;font-weight:600;border-radius:99px;padding:3px 10px;margin-right:8px}
.toast{position:fixed;left:50%;bottom:calc(env(safe-area-inset-bottom,0px) + 22px);transform:translate(-50%,30px);background:#0f172a;color:#fff;padding:11px 20px;border-radius:12px;font-size:14px;font-weight:600;opacity:0;pointer-events:none;transition:opacity .2s,transform .2s;z-index:50;max-width:90vw;border:1px solid #1e293b}
.toast.on{opacity:1;transform:translate(-50%,0)}
footer.pg{padding:22px 16px 46px;color:var(--mute);font-size:13px;text-align:center}
@media (prefers-reduced-motion:reduce){.toast{transition:none}}
@media (max-width:640px){.pillb{font-size:13px;padding:8px 13px}.info{padding:14px 16px}.stagebox{border-radius:22px}}

/* ===== annotation pills ===== */
.an{position:relative}
.pinon .an{outline:1.5px dashed rgba(79,70,229,.55);outline-offset:3px;border-radius:8px;margin-top:20px;padding-top:8px}
.pinon .an::before{content:attr(data-label);position:absolute;top:-15px;left:10px;z-index:6;background:#4f46e5;color:#fff;font:600 10px/1 var(--mono);padding:5px 10px;border-radius:99px;white-space:nowrap;max-width:calc(100% - 14px);overflow:hidden;text-overflow:ellipsis;box-shadow:0 2px 6px rgba(79,70,229,.35)}

/* ===== wireframe kit ===== */
.fr{--w-bg:#fff;--w-fill:#eceef1;--w-line:#cfd3da;--w-ink:#252a31;--w-mute:#767c87;--w-soft:#f6f7f9;--pri:#252a31;--jade:#252a31;
  font:13px/1.45 var(--font);color:var(--w-ink);background:var(--w-bg);text-align:left}
.fr.m{min-height:780px;border:8px solid #0f172a;border-radius:40px;overflow:hidden;display:flex;flex-direction:column;flex:none;box-shadow:0 10px 30px rgba(15,23,42,.25)}
.fr.d{width:1366px;border:1px solid #cbd5e1;border-radius:20px;overflow:hidden;transform-origin:top left;box-shadow:0 10px 30px rgba(15,23,42,.18)}
.fr.free{width:100%;border:1px solid var(--w-line);border-radius:20px;padding:26px}
.notch{display:flex;justify-content:space-between;padding:8px 20px 4px;font-size:11px;font-weight:600;color:var(--w-mute);background:inherit}
.chrome{display:flex;align-items:center;gap:8px;background:#fff;padding:14px 22px;border-bottom:1px solid #e2e8f0}
.chrome i{width:16px;height:16px;border-radius:50%;display:block}
.chrome i:nth-child(1){background:#ff5f57}.chrome i:nth-child(2){background:#febc2e}.chrome i:nth-child(3){background:#28c840}
.chrome .url{margin:0 auto;background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:6px 16px;font:500 13px var(--mono);color:#475569;width:min(60%,720px);text-align:center;transform:translateX(-6%)}
.chrome .rl{color:#94a3b8;font-size:16px;margin-left:auto}
.ph{background:
  linear-gradient(to top right,transparent calc(50% - .6px),var(--w-line) 50%,transparent calc(50% + .6px)),
  linear-gradient(to top left,transparent calc(50% - .6px),var(--w-line) 50%,transparent calc(50% + .6px)),var(--w-fill);
  border:1px solid var(--w-line);border-radius:8px;display:flex;align-items:center;justify-content:center;color:var(--w-mute);font-size:11px;gap:6px}
.ph span{background:var(--w-fill);padding:0 6px}
.th{width:34px;height:34px;border-radius:6px;flex:none}
.ic{width:16px;height:16px;border:1.5px solid var(--w-mute);border-radius:4px;display:inline-block;flex:none}
.logo{display:flex;align-items:center;gap:8px;font-weight:700;font-size:14px;cursor:pointer}
.logo b{border:1.5px solid var(--w-ink);border-radius:6px;padding:2px 7px;font-size:11px}
.logo em{font-style:normal}
.logo.big{justify-content:center;margin-bottom:8px}.logo.big b{padding:14px 22px;font-size:14px}
.tagb{border:1px solid var(--w-line);border-radius:6px;padding:2px 10px;font-size:12px;color:var(--w-mute)}
.h1{font-size:22px;font-weight:700;letter-spacing:-.01em}.h2{font-size:16px;font-weight:700}.mute{color:var(--w-mute)}.sm{font-size:11px}
.row{display:flex;gap:8px;align-items:center}.sb{justify-content:space-between}.col{display:flex;flex-direction:column;gap:8px}
.btn{display:inline-block;padding:9px 14px;border:1.5px solid var(--w-ink);border-radius:8px;font-weight:600;text-align:center;background:#fff;white-space:nowrap;cursor:pointer;user-select:none}
.btn.p{background:var(--w-ink);color:#fff}.btn.s{border-color:var(--w-line);color:var(--w-ink)}.btn.dg{border-color:#c0392b;color:#c0392b}
.btn.off{opacity:.4;cursor:not-allowed}
.btn.block{display:block;width:100%}.btn.xs{padding:4px 9px;font-size:12px;border-width:1px}
.fld{margin-bottom:10px}.lbl{font-size:11px;font-weight:600;margin-bottom:4px}
.inp{border:1.5px solid var(--w-line);border-radius:8px;padding:9px 10px;color:var(--w-mute);background:#fff;min-height:38px}
.inp.ta{min-height:70px}.inp.sel::after{content:"▾";float:right}
.search{border:1.5px solid var(--w-line);border-radius:10px;padding:9px 12px;color:var(--w-mute);background:var(--w-soft)}
.w320{width:340px}
.chips{display:flex;gap:6px;margin:10px 0 12px;overflow:auto;scrollbar-width:none}.chips::-webkit-scrollbar{display:none}
.chip{border:1px solid var(--w-line);border-radius:99px;padding:5px 12px;font-size:12px;white-space:nowrap;background:#fff;cursor:pointer}.chip.on{background:var(--w-ink);color:#fff;border-color:var(--w-ink)}
.card{border:1px solid var(--w-line);border-radius:10px;padding:12px;background:#fff}
.card.soft{background:var(--w-soft)}.card[data-go]{cursor:pointer}
.badge{display:inline-block;border-radius:99px;padding:2px 9px;font-size:11px;font-weight:600;white-space:nowrap}
.b-amber{background:#fdf0cf;color:#7a5600}.b-blue{background:#dceafd;color:#1c4c94}.b-violet{background:#e9e0fb;color:#4e2a95}
.b-green{background:#d9f2e0;color:#1d6b38}.b-red{background:#fbdcdc;color:#932222}
.qty{display:inline-flex;align-items:center;border:1.5px solid var(--w-line);border-radius:8px;overflow:hidden}
.qty b{padding:3px 10px;cursor:pointer;user-select:none}.qty i{font-style:normal;padding:3px 10px;border-left:1px solid var(--w-line);border-right:1px solid var(--w-line);min-width:34px;text-align:center}
.tg{width:34px;height:19px;border-radius:99px;background:var(--w-line);display:inline-block;position:relative;flex:none;cursor:pointer}
.tg i{position:absolute;top:2px;left:2px;width:15px;height:15px;border-radius:50%;background:#fff;transition:left .15s}
.tg.on{background:var(--jade)}.tg.on i{left:17px}
.grid{display:grid;gap:12px;grid-template-columns:repeat(var(--c,2),1fr)}
.gp{--c:2}.d .gp{--c:4}.gs{--c:2}.d .gs{--c:4}
.split{display:grid;gap:16px;grid-template-columns:1fr}.d .split{grid-template-columns:var(--cols,1fr 1fr)}
.fr.m .pc{padding:10px}.fr.m .pc .pn{font-size:12.5px}.pc .pn{font-weight:600;margin-top:8px}.pc .pp{color:var(--w-mute);margin:2px 0 8px}
.lazy{text-align:center;color:var(--w-mute);padding:14px 0 4px;font-size:12px}
.mh{display:flex;align-items:center;justify-content:space-between;padding:8px 14px;border-bottom:1px solid var(--w-line);gap:8px}
.mb{flex:1;padding:14px}.mb.center{display:flex;align-items:center;justify-content:center}
.bn{display:flex;border-top:1px solid var(--w-line);background:#fff}
.bi{flex:1;text-align:center;font-size:10px;padding:7px 0 8px;color:var(--w-mute);display:flex;flex-direction:column;align-items:center;gap:3px;cursor:pointer}.bi.on{color:var(--w-ink);font-weight:700}.bi.on .ic{border-color:var(--w-ink);background:var(--w-ink)}
.cartb{border:1.5px solid var(--w-ink);border-radius:8px;padding:4px 10px;font-weight:600;display:inline-flex;gap:6px;align-items:center;cursor:pointer}
.cartb em{background:var(--w-ink);color:#fff;border-radius:99px;font-style:normal;font-size:10px;padding:0 6px}
.dh{display:flex;align-items:center;gap:26px;padding:12px 48px;border-bottom:1px solid var(--w-line)}
.dn{display:flex;gap:22px;flex:1;justify-content:center}.dn span{color:var(--w-mute);cursor:pointer}.dn span.on{color:var(--w-ink);font-weight:700;border-bottom:2px solid var(--pri)}
.dbody{padding:26px 48px 34px;min-height:560px}
.dfoot{display:flex;justify-content:space-between;padding:18px 48px;border-top:1px solid var(--w-line);background:var(--w-soft);color:var(--w-mute);font-size:12px}
.crumb{font-size:12px;color:var(--w-mute);margin-bottom:10px}.crumb b{color:var(--pri);font-weight:600}
.line{height:8px;background:var(--w-fill);border-radius:4px;margin:7px 0}
.sumrow{display:flex;justify-content:space-between;padding:4px 0;gap:10px}.sumrow.t{border-top:1px dashed var(--w-line);margin-top:6px;padding-top:9px;font-weight:700;font-size:15px}
.item{display:flex;gap:10px;align-items:center;padding:10px 0;border-bottom:1px solid var(--w-line)}.item:last-child{border:0}
.item .nm{flex:1;min-width:0}.item .nm b{display:block}
.ok{color:#1d6b38;font-size:12px;margin-top:6px}.err{color:#b3261e;font-size:12px;margin:6px 0 10px}
.state{font-size:10px;background:var(--w-fill);color:var(--w-mute);border-radius:4px;padding:1px 6px;margin-right:4px}
.hs{display:flex;margin:10px 0}.hs div{flex:1;text-align:center;font-size:11px;color:var(--w-mute)}
.hs div::before{content:"";display:block;width:16px;height:16px;border-radius:50%;background:#fff;border:2px solid var(--w-line);margin:0 auto 5px}
.hs .done{color:var(--w-ink)}.hs .done::before{background:var(--jade);border-color:var(--jade)}
.hs .cur{font-weight:700;color:var(--w-ink)}.hs .cur::before{background:#fff;border-color:var(--jade);box-shadow:0 0 0 3px var(--w-fill)}
.tbl{width:100%;border-collapse:collapse}.tbl th{text-align:left;font-size:11px;color:var(--w-mute);font-weight:600;padding:8px;border-bottom:1.5px solid var(--w-line);background:var(--w-soft)}
.tbl td{padding:9px 8px;border-bottom:1px solid var(--w-line);vertical-align:middle}
.tc{margin-bottom:8px}.tc .tt{font-weight:700;margin-bottom:6px}.tc .kv{display:flex;justify-content:space-between;gap:10px;padding:2px 0;font-size:12px}.tc .kv span{color:var(--w-mute)}.tc .ac{margin-top:6px;display:flex;gap:6px}
.stat{padding:12px}.stat .v{font-size:24px;font-weight:700;line-height:1.2}.stat .l{font-size:12px;color:var(--w-mute)}
.ad{display:flex;min-height:720px}.sbar{width:232px;background:var(--w-soft);border-right:1px solid var(--w-line);padding:16px 12px;display:flex;flex-direction:column;gap:4px;flex:none}
.sbar .role{font-size:11px;color:var(--w-mute);margin:2px 0 14px}.mi{display:flex;align-items:center;gap:10px;padding:9px 11px;border-radius:8px;color:var(--w-mute);cursor:pointer}
.mi.on{background:var(--pri);color:#fff}.mi.on .ic{border-color:#fff}.mi.out{margin-top:auto;border:1px solid var(--w-line);color:var(--w-ink)}
.mn{flex:1;min-width:0}.tb{display:flex;justify-content:space-between;align-items:center;padding:14px 28px;border-bottom:1px solid var(--w-line)}
.ct{padding:22px 28px 30px}
.av{border:1.5px solid var(--w-ink);border-radius:99px;padding:2px 10px;font-size:12px;font-weight:600;cursor:pointer}
.ro{background:#fdf0cf;color:#7a5600;border:1px solid #ecd58e;border-radius:8px;padding:7px 12px;font-size:12px;margin-bottom:14px}
.lsplit{display:grid;grid-template-columns:1fr 1fr;min-height:640px}.lbrand{background:var(--w-ink);color:#fff;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px}
.lbrand .logo b{border-color:#fff;color:#fff}.lform{display:flex;align-items:center;justify-content:center;padding:20px}
.lg{width:100%;max-width:330px}.lg .h2{text-align:center;font-size:18px}.lg .mute{text-align:center;margin-bottom:14px}
.hbar{height:16px;background:var(--jade);border-radius:4px}
.hb{display:grid;grid-template-columns:100px 1fr 34px;gap:8px;align-items:center;margin:8px 0;font-size:12px}
.vbars{display:flex;align-items:flex-end;gap:10px;height:130px;padding-top:8px;border-bottom:1.5px solid var(--w-line)}.vbars i{flex:1;background:var(--jade);border-radius:4px 4px 0 0;opacity:.9}
.pill{display:flex;gap:6px;flex-wrap:wrap;margin-bottom:10px}
.ck{display:inline-block;width:14px;height:14px;border:1.5px solid var(--w-ink);border-radius:3px;vertical-align:-2px;margin-right:4px}.ck.on{background:var(--w-ink)}
.dz{border:2px dashed var(--w-line);border-radius:10px;padding:18px;text-align:center;color:var(--w-mute);background:var(--w-soft)}
.okbig{width:60px;height:60px;border-radius:50%;border:3px solid var(--jade);color:var(--jade);display:flex;align-items:center;justify-content:center;font-size:28px;margin:0 auto 10px}
.ref{border:1.5px dashed var(--pri);border-radius:8px;padding:10px;text-align:center;font-weight:700;letter-spacing:.04em;font-size:16px}

/* ===== Hi-Fi Polished (Terracotta Accent) ===== */
.fr.hi{--w-ink:#2B2118;--w-mute:#8A7E74;--w-line:#E6E1DC;--w-fill:#FDF3E8;--w-soft:#FFF7ED;--w-bg:#FFFFFF;--pri:#B5451A;--jade:#1E9E5A}
.fr.hi .logo b{display:none}
.fr.hi .logo em{color:var(--pri)}
.fr.hi .dh .logo em,.fr.hi .mh .logo em,.fr.hi .sbar .logo em{color:#FFA382}
.fr.hi .logo.big span{font-size:24px;font-weight:800}
.fr.hi .dh .logo,.fr.hi .mh .logo,.fr.hi .sbar .logo,.fr.hi .lbrand .logo{color:#fff}
.fr.hi .dh .logo{font-size:22px;font-weight:800;letter-spacing:-.01em}
.fr.hi .dh{background:#1A1A1E;border:0;padding:16px 48px}
.fr.hi .tagb{border-color:rgba(255,163,130,.45);color:#FFA382;background:rgba(255,255,255,.04)}
.fr.hi .dn span{color:#cbd5e1;font-weight:600}.fr.hi .dn span.on{color:#fff;border-bottom:2px solid var(--pri)}
.fr.hi .mh{background:#1A1A1E;border:0}.fr.hi .mh b{color:#fff}.fr.hi .mh .ic{border-color:#94a3b8}.fr.hi .mh .av{color:#fff;border-color:#475569}
.fr.hi .notch{background:#1A1A1E;color:#cbd5e1;padding-bottom:6px}
.fr.hi .cartb{background:var(--pri);border-color:var(--pri);color:#fff;border-radius:99px}.fr.hi .cartb em{background:#fff;color:var(--pri)}
.fr.hi .btn{border-radius:12px;border-color:var(--pri);color:var(--pri);background:#fff}
.fr.hi .btn.p{background:var(--pri);color:#fff}
.fr.hi .btn.s{border-color:#cbd5e1;color:#0f172a}
.fr.hi .btn.dg{border-color:#fecaca;color:#dc2626}
.fr.hi .chip{background:#fff;border-color:#cbd5e1;color:#334155;font-weight:600}
.fr.hi .chip.on{background:var(--pri);border-color:var(--pri);color:#fff}
.fr.hi .card{border-radius:16px;border-color:#E6E1DC;box-shadow:0 2px 8px rgba(43,33,24,.04)}
.fr.hi .card.soft{background:#FDF3E8;box-shadow:none}
.fr.hi .inp{border-radius:10px;border-color:#cbd5e1;color:#8A7E74}
.fr.hi .search{background:#fff;border-radius:12px;border-color:#cbd5e1}
.fr.hi .bn{background:#fff}.fr.hi .bi.on{color:var(--pri)}.fr.hi .bi.on .ic{background:var(--pri);border-color:var(--pri)}
.fr.hi .dbody,.fr.hi .mb,.fr.hi .ct{background:#F4F4FA}
.fr.hi .dfoot{background:#1A1A1E;color:#94a3b8;border:0}
.fr.hi .sbar{background:#1A1A1E;border:0}.fr.hi .mi{color:#cbd5e1}.fr.hi .mi .ic{border-color:#64748b}
.fr.hi .mi.on{background:var(--pri);color:#fff}.fr.hi .mi.on .ic{border-color:#fff}
.fr.hi .mi.out{border-color:#334155;color:#cbd5e1}.fr.hi .sbar .role{color:#94a3b8}
.fr.hi .tb{background:#fff}
.fr.hi .lbrand{background:#1A1A1E}
.fr.hi .ph{background:#FDF3E8;border-color:#E6E1DC;color:#8A7E74;flex-direction:column;font-weight:600}
.fr.hi .ph::before{content:"🥟";font-size:30px;line-height:1;opacity:.65}
.fr.hi .ph.th::before{font-size:16px}
.fr.hi .ph span{background:transparent;padding:0}
.fr.hi .h1{font-weight:800}.fr.hi.d .h1{font-size:34px}
.fr.hi .tbl th{background:#FDF3E8}
.fr.hi .ref{background:#FDF3E8;color:#B5451A}
.fr.hi .crumb{color:#8A7E74}
.fr.hi .hs div::before{background:#f8fafc}
.fr.hi .hs .done::before{background:var(--jade);border-color:var(--jade)}
.fr.hi .hs .cur::before{background:#fff;border-color:var(--jade)}
.fr.hi .stat .v{font-weight:800}

/* ===== design system page ===== */
.dsx .tbl td,.dsx .tbl th{font-size:12px}.dsx h3{font-size:15px;margin:22px 0 10px}.dsx h3:first-child{margin-top:0}
.dsg{display:grid;gap:12px;grid-template-columns:repeat(auto-fit,minmax(150px,1fr))}
.sw{border:1px solid var(--w-line);border-radius:12px;overflow:hidden;background:#fff}.sw i{display:block;height:54px}.sw div{padding:7px 10px;font-size:12px}.sw b{display:block}
.dsx .spc{display:flex;gap:14px;align-items:flex-end}.dsx .spc div{background:var(--w-fill);border:1px solid var(--w-line);text-align:center;font-size:11px;padding-top:2px}
</style>
</head>
<body>
<header class="top">
  <div class="l">
    <span class="sprint">SPRINT 1 UI/UX</span>
    <div><h1>Interactive Wireframe Showcase • Dapur Th Sant</h1><p>Pedoman Visual Tata Letak Katalog &amp; Order Online UMKM untuk Web Responsif (Desktop dan Seluler)</p></div>
  </div>
  <div class="r">
    <button class="pillb" id="modeb">🎨 <span id="modet">Mode: Hi-Fi Polished</span></button>
    <button class="pillb w" id="annob">🏷️ <span id="annot">Sembunyikan Anotasi FR/US</span></button>
  </div>
</header>
<nav class="tabsbar" id="tabs" aria-label="Pilih layar" role="tablist"><span class="lb">PILIH LAYAR:</span></nav>

<main class="main">
  <section class="info">
    <div class="t" id="info"></div>
    <div class="dev" id="devbar">
      <div class="seg" id="dev"><button data-v="d" aria-pressed="true">🖥️ Desktop (1366px)</button><button data-v="m" aria-pressed="false">📱 Mobile (390px)</button></div>
      <span id="scl" style="font-size:12px;color:var(--mute)"></span>
      <div class="seg sm" id="mw" hidden><button data-v="360" aria-pressed="false">360</button><button data-v="390" aria-pressed="true">390</button><button data-v="414" aria-pressed="false">414</button></div>
    </div>
  </section>
  <section class="stagebox"><div class="stagein" id="stage"></div></section>
  <section class="notes" id="notes" hidden></section>
</main>
<footer class="pg">Spesifikasi Wireframe &amp; Desain Antarmuka • Katalog &amp; Order Online Dapur Th Sant • Tim SATURN • Acuan: Proposal Sprint 0 Baseline v1.0 &amp; Skema ERD</footer>
<div class="toast" id="toast" role="status" aria-live="polite"></div>

<script>
/* ---------- helpers ---------- */
const rp=n=>'Rp '+n.toLocaleString('id-ID');
const ph=(h,l='')=>`<div class="ph" style="height:${h}px">${l?`<span>${l}</span>`:''}</div>`;
const btn=(t,k='p',b=false,x='',go='')=>`<span class="btn ${k} ${b?'block':''} ${x}" ${go?`data-go="${go}"`:''}>${t}</span>`;
const inp=(l,v='',c='')=>`<div class="fld"><div class="lbl">${l}</div><div class="inp ${c}">${v}</div></div>`;
const chip=(t,on)=>`<span class="chip ${on?'on':''}">${t}</span>`;
let AN=[];let CARTN=2;
const an=(n,h)=>{const a=AN[n-1]||['','',''];return `<div class="an" data-label="${a[0]}: ${a[1]}">${h}</div>`};

/* Status Order Sesuai ERD enum: status */
const ST={mk:['Menunggu Konfirmasi','b-amber'],dp:['Diproses','b-blue'],dk:['Dikirim','b-violet'],sl:['Selesai','b-green'],db:['Dibatalkan','b-red']};
const bd=k=>`<span class="badge ${ST[k][1]}">${ST[k][0]}</span>`;

/* Status Pembayaran Sesuai ERD payments: payment_status */
const PST={
  pr:['PENDING_REVIEW (Menunggu Verifikasi)','b-amber'],
  vf:['VERIFIED (Lunas)','b-green'],
  rj:['REJECTED (Ditolak)','b-red']
};
const pbd=k=>`<span class="badge ${PST[k][1]}">${PST[k][0]}</span>`;

const qty=n=>`<span class="qty"><b>−</b><i>${n}</i><b>+</b></span>`;
const tg=on=>`<span class="tg ${on?'on':''}"><i></i></span>`;
const th=()=>`<span class="ph th"></span>`;
const notch=()=>`<div class="notch"><span>09:41</span><span>4G ▮▮▮</span></div>`;
const chrome=u=>`<div class="chrome"><i></i><i></i><i></i><div class="url">https://${u}</div><span class="rl">↻</span></div>`;
const PR=[
 {n:'Dimsum Ayam',h:25000,s:'Stok 24',k:'Kukus'},{n:'Siomay Udang',h:28000,s:'Stok 18',k:'Kukus'},
 {n:'Hakau Udang',h:30000,s:'Stok 9',k:'Kukus'},{n:'Lumpia Kulit Tahu',h:22000,s:'Stok 30',k:'Goreng'},
 {n:'Bakpao Ayam',h:20000,s:'Stok 12',k:'Kukus'},{n:'Pangsit Goreng',h:18000,s:'Stok habis',k:'Goreng'},
 {n:'Dimsum Frozen 20 pcs',h:65000,s:'Stok 7',k:'Frozen'},{n:'Paket Keluarga',h:120000,s:'Stok 5',k:'Paket'}];
const prodCard=(p,go=true)=>{const x=p.s==='Stok habis';return `<div class="card pc" ${go&&!x?'data-go="P2"':''}>${ph(100)}<div class="pn">${p.n}</div><div class="pp">${rp(p.h)}</div><div class="row sb"><span class="badge ${x?'b-red':'b-green'}">${p.s}</span>${x?'<span class="btn s xs">Habis</span>':'<span class="btn p xs">+ Cart</span>'}</div></div>`};
function tbl(h,rows,m){
  if(!m) return `<table class="tbl"><thead><tr>${h.map(x=>`<th>${x}</th>`).join('')}</tr></thead><tbody>${rows.map(r=>`<tr>${r.map(c=>`<td>${c}</td>`).join('')}</tr>`).join('')}</tbody></table>`;
  return rows.map(r=>`<div class="card tc"><div class="tt">${r[0]}</div>${r.slice(1).map((c,i)=>h[i+1]===''?`<div class="ac">${c}</div>`:`<div class="kv"><span>${h[i+1]}</span><b>${c}</b></div>`).join('')}</div>`).join('');
}

/* Data Order Mockup Sinkron dengan ERD (orders & payments) */
const OR=[
  ['DST-260924-0012','Rina Aulia',rp(90000),'mk','TRANSFER_BANK','pr'],
  ['DST-260924-0011','Budi Santoso',rp(125000),'dp','TRANSFER_BANK','vf'],
  ['DST-260923-0010','Sari Wulandari',rp(48000),'dk','COD','vf'],
  ['DST-260923-0009','Deni Pratama',rp(210000),'sl','TRANSFER_BANK','vf'],
  ['DST-260922-0008','Maya Putri',rp(56000),'db','TRANSFER_BANK','rj']
];

const hs=cur=>{const o=['mk','dp','dk','sl'],i=o.indexOf(cur);return `<div class="hs">${o.map((k,j)=>`<div class="${j<i?'done':j===i?'cur':''}">${ST[k][0]}</div>`).join('')}</div>`};
const stat=(l,v,s='')=>`<div class="card stat"><div class="l">${l}</div><div class="v">${v}</div><div class="l">${s}</div></div>`;

/* ---------- shells ---------- */
function cust(m,act,body,url){
  const cart=`<span class="cartb" data-go="P3">Cart <em>${CARTN}</em></span>`;
  const logo=`<div class="logo" data-go="P1"><b>LOGO</b><span>Dapur <em>Th Sant</em></span></div>`;
  if(m){
    const nav=[['Katalog','k','P1'],['Cart','c','P3'],['Status','s','P6'],['Kontak','o','P7']];
    return notch()+`<div class="mh">${logo}${cart}</div><div class="mb">${body}</div><div class="bn">${nav.map(x=>`<div class="bi ${x[1]===act?'on':''}" data-go="${x[2]}"><span class="ic"></span>${x[0]}</div>`).join('')}</div>`;
  }
  return chrome(url)+`<div class="dh">${logo}<span class="tagb">Dimsum Rumahan</span><div class="dn"><span class="${act==='k'?'on':''}" data-go="P1">Katalog</span><span class="${act==='s'?'on':''}" data-go="P6">Status Order</span><span class="${act==='o'?'on':''}" data-go="P7">Kontak</span></div>${cart}</div><div class="dbody">${body}</div><div class="dfoot"><span>© Dapur Th Sant</span><span>WhatsApp · Instagram · Alamat</span></div>`;
}
const AM=[['Dashboard','A2'],['Produk &amp; Kategori','A3a'],['Order','A4'],['Kode Promo','A6'],['Kontak','A7']];
const OM=[['Dashboard','O2'],['Laporan','O3']];
function adm(m,menu,act,title,body,url,role='Admin'){
  const out=role==='Owner'?'O1':'A1';
  if(m) return notch()+`<div class="mh"><span class="ic"></span><b>${title}</b><span class="av" data-go="${out}">${role[0]}</span></div><div class="mb">${body}</div><div class="bn">${menu.map(x=>`<div class="bi ${x[1]===act?'on':''}" data-go="${x[1]}"><span class="ic"></span>${({'Kode Promo':'Promo'}[x[0]]||x[0].split(' ')[0])}</div>`).join('')}</div>`;
  return chrome(url)+`<div class="ad"><aside class="sbar"><div class="logo"><b>LOGO</b><span>Dapur <em>Th Sant</em></span></div><div class="role">Panel ${role}</div>${menu.map(x=>`<div class="mi ${x[1]===act?'on':''}" data-go="${x[1]}"><span class="ic"></span>${x[0]}</div>`).join('')}<div class="mi out" data-go="${out}">Logout</div></aside><section class="mn"><div class="tb"><span class="h2">${title}</span><span class="av">${role}</span></div><div class="ct">${body}</div></section></div>`;
}
function login(m,role,user,url,go){
  const f=`<div class="lg"><div class="logo big"><b>LOGO</b><span>Dapur <em>Th Sant</em></span></div><div class="h2">Masuk ${role}</div><div class="mute">Gunakan akun yang terdaftar</div>${an(1,inp('Username / email',user)+inp('Password','••••••••'))}${an(2,`<div class="err"><span class="state">state error</span>Username atau password salah. Periksa lagi lalu coba masuk.</div>`)}${btn('Masuk','p',true,'',go)}</div>`;
  return m?notch()+`<div class="mb center">${f}</div>`:chrome(url)+`<div class="lsplit"><div class="lbrand"><div class="logo"><b>LOGO</b></div><div class="h1">Dapur Th Sant</div><div>Katalog &amp; Order Online</div></div><div class="lform">${f}</div></div>`;
}
const jamRows=[['Senin – Jumat','08.00 – 17.00'],['Sabtu','08.00 – 15.00'],['Minggu','Tutup']];

/* ---------- SCREENS ---------- */
const SC=[];
const add=o=>SC.push(o);

add({id:'P1',g:'Pelanggan',t:'Beranda / Katalog',h:'Katalog Produk: Cari, Filter, dan Pilih Dimsum',route:'/',fr:'FR01',
 an:[['FR01','Kolom pencarian produk berdasarkan nama.'],['FR01','Chip filter kategori. Hasil berubah tanpa pindah halaman.'],['FR01 · NFR01','Grid produk: 2 kolom di seluler, 4 kolom di desktop. Gambar dimuat bertahap (lazy load) agar halaman terbuka < 3 detik di 4G.'],['FR03','Produk stok habis: badge merah dan tombol tambah dinonaktifkan.']],
 r:m=>cust(m,'k',`${m?an(1,`<div class="search">⌕ Cari produk…</div>`):`<div class="row sb"><div class="h1">Katalog Produk</div>${an(1,`<div class="search w320">⌕ Cari produk…</div>`)}</div>`}
 ${an(2,`<div class="chips">${['Semua','Kukus','Goreng','Frozen','Paket'].map((c,i)=>chip(c,!i)).join('')}</div>`)}
 ${an(3,`<div class="grid gp">${PR.slice(0,m?6:8).map(p=>prodCard(p)).join('')}</div>`)}<div class="lazy">↓ Memuat produk berikutnya (lazy load)</div>`,'dapurthsant.id/')});

add({id:'P2',g:'Pelanggan',t:'Detail Produk',h:'Detail Produk: Foto, Harga, Stok, dan Tambah ke Cart',route:'/produk/{slug}',fr:'FR02',
 an:[['FR02','Foto produk berukuran besar.'],['FR02','Nama, harga, dan badge stok.'],['FR03','Pilih jumlah lalu Tambah ke Cart. Pengguna tetap di halaman ini dan angka di ikon cart bertambah.'],['FR02','Produk nonaktif atau stok habis: tombol menjadi "Stok habis" dan tidak bisa diklik.']],
 r:m=>cust(m,'k',`${m?'':`<div class="crumb">Katalog › Kukus › <b>Dimsum Ayam</b></div>`}<div class="split" style="--cols:1fr 1fr">${an(1,ph(m?230:400,'Foto produk'))}<div>
  ${an(2,`<div class="h1">Dimsum Ayam</div><div class="h2" style="margin:4px 0 8px">${rp(25000)}</div><span class="badge b-green">Stok 24</span>`)}
  <div style="margin:14px 0"><div class="lbl">Deskripsi</div><div class="line"></div><div class="line"></div><div class="line" style="width:70%"></div></div>
  ${an(3,`<div class="row" style="margin-bottom:12px"><span class="lbl" style="margin:0">Jumlah</span>${qty(1)}</div><span class="btn p block" data-toast="Ditambahkan ke cart. Badge cart +1">Tambah ke Cart</span>`)}
  <div class="ok"><span class="state">toast</span>✓ Ditambahkan ke cart</div></div></div>`,'dapurthsant.id/produk/dimsum-ayam')});

const cartItems=[['Dimsum Ayam',25000,2],['Siomay Udang',28000,1],['Lumpia Kulit Tahu',22000,1]].map(x=>`<div class="item">${th()}<div class="nm"><b>${x[0]}</b><span class="mute">${rp(x[1])}</span></div>${qty(x[2])}<span class="btn dg xs">Hapus</span></div>`).join('');
add({id:'P3',g:'Pelanggan',t:'Cart (Keranjang)',h:'Cart: Ubah Jumlah, Hapus Item, Total Otomatis',route:'/cart',fr:'FR03',
 an:[['FR03','Ubah jumlah dengan stepper atau hapus item. Klik tanda + atau − untuk mencoba.'],['FR03 · NFR05','Subtotal dan total dihitung otomatis setiap ada perubahan.'],['FR04','Tombol Checkout. Nonaktif dan diganti ajakan "Lihat katalog" bila cart kosong.']],
 r:m=>cust(m,'c',`<div class="h1" style="margin-bottom:10px">Cart Anda</div><div class="split" style="--cols:1.6fr 1fr">${an(1,`<div class="card">${cartItems}</div>`)}
 <div style="align-self:start">${an(2,`<div class="card soft"><div class="h2" style="margin-bottom:8px">Ringkasan</div><div class="sumrow"><span>Subtotal (4 item)</span><span>${rp(100000)}</span></div><div class="sumrow t"><span>Total</span><span>${rp(100000)}</span></div></div>`)}<div style="margin-top:12px">${an(3,btn('Lanjut ke Checkout','p',true,'','P4'))}</div></div></div>`,'dapurthsant.id/cart')});

/* P4: Checkout Ditambahkan Pilihan payment_method (ERD: orders.payment_method) */
add({id:'P4',g:'Pelanggan',t:'Checkout',h:'Checkout: Data Pemesan, Metode Bayar, Promo & Ringkasan',route:'/checkout',fr:'FR04 · FR11 · ERD-Orders',
 an:[
   ['FR04','Form data pemesan (customer_name, customer_phone, shipping_address).'],
   ['ERD Orders','Pilihan metode bayar (payment_method: TRANSFER_BANK / COD).'],
   ['FR11 · US-004','Kode promo (promos.code) tervalidasi otomatis terhadap total belanja.'],
   ['FR04 · NFR05','Ringkasan dan total akhir order (subtotal_amount, discount_amount, total_amount).']
 ],
 r:m=>cust(m,'c',`<div class="h1" style="margin-bottom:10px">Checkout</div><div class="split" style="--cols:1.4fr 1fr"><div class="col">
 ${an(1,`<div class="card"><div class="h2" style="margin-bottom:8px">Data pemesan</div>${inp('Nama lengkap (customer_name)','Rina Aulia')}${inp('Nomor WhatsApp (customer_phone)','0812-3456-7890')}${inp('Alamat pengiriman (shipping_address)','Jl. Cikareo Legok, RT 02/03, Alamendah, Kec. Rancabali','ta')}</div>`)}
 ${an(2,`<div class="card"><div class="h2" style="margin-bottom:8px">Metode Pembayaran (payment_method)</div>
   <div style="display:flex;flex-direction:column;gap:8px;font-size:13px;">
     <label style="display:flex;align-items:center;gap:8px;cursor:pointer;"><input type="radio" name="pm" checked> <span><b>Transfer Bank / QRIS</b> (Upload Bukti Bayar)</span></label>
     <label style="display:flex;align-items:center;gap:8px;cursor:pointer;"><input type="radio" name="pm"> <span><b>COD</b> (Bayar Tunai saat Terima / Ambil di Tempat)</span></label>
   </div>
 </div>`)}
 ${an(3,`<div class="card"><div class="h2" style="margin-bottom:8px">Kode promo</div><div class="row"><div class="inp" style="flex:1">HEMAT10</div>${btn('Terapkan','s')}</div>
 <div class="ok"><span class="state">valid</span>✓ HEMAT10 diterapkan, potongan ${rp(10000)}.</div>
 <div class="err" style="margin-bottom:0"><span class="state">tidak valid</span>Kode sudah kedaluwarsa. Total tidak berubah.</div></div>`)}</div>
 <div style="align-self:start">${an(4,`<div class="card soft"><div class="h2" style="margin-bottom:8px">Ringkasan order</div><div class="sumrow"><span>Subtotal (subtotal_amount)</span><span>${rp(100000)}</span></div><div class="sumrow"><span>Diskon Promo (discount_amount)</span><span>− ${rp(10000)}</span></div><div class="sumrow t"><span>Total Akhir (total_amount)</span><span>${rp(90000)}</span></div><div style="margin-top:12px">${btn('Buat Order Sekarang','p',true,'','P5')}</div></div>`)}</div></div>`,'dapurthsant.id/checkout')});

/* P5: Konfirmasi Order Ditambahkan Upload Bukti Transfer (ERD: payments.receipt_image_path) */
add({id:'P5',g:'Pelanggan',t:'Konfirmasi Order',h:'Konfirmasi: Nomor Referensi & Bukti Pembayaran',route:'/order/berhasil',fr:'FR04 · ERD-Payments',
 an:[
   ['FR04','Nomor referensi unik pesanan (order_ref).'],
   ['ERD Payments','Instruksi transfer & form upload struk bukti bayar (receipt_image_path).'],
   ['ERD Payments','Status awal pembayaran tercatat sebagai PENDING_REVIEW sampai diverifikasi Admin.']
 ],
 r:m=>cust(m,'c',`<div style="max-width:480px;margin:${m?'8px':'20px'} auto"><div class="okbig">✓</div><div class="h1" style="text-align:center">Order berhasil dibuat!</div><div class="mute" style="text-align:center;margin:4px 0 16px">Simpan nomor referensi ini untuk melacak order Anda.</div>
 ${an(1,`<div class="ref">DST-260924-0012</div><div style="text-align:center;margin:8px 0 6px">${btn('Salin nomor referensi','s','','xs')}</div>`)}

 <div style="margin-top:14px">${an(2,`<div class="card">
   <div class="h2" style="margin-bottom:6px">Instruksi Pembayaran Transfer Bank</div>
   <div style="font-size:12px;color:var(--mute);margin-bottom:10px;">Silakan transfer sejumlah <b>${rp(90000)}</b> ke rekening outlet:</div>
   <div style="background:var(--w-soft);padding:10px;border-radius:8px;font-size:12px;margin-bottom:12px;">
     <div>Bank: <b>BCA / Mandiri</b></div>
     <div>No. Rek: <b>139-00-249581-2 (Dapur Th Sant)</b></div>
   </div>
   <div class="lbl">Unggah Bukti Transfer (receipt_image_path)</div>
   <div class="dz" style="padding:14px;margin-bottom:10px;">
     📄 Tarik foto bukti transfer ke sini atau <span style="color:var(--teal);font-weight:700;cursor:pointer;">Pilih File</span>
     <div style="font-size:10px;color:var(--mute);margin-top:4px;">Format JPG/PNG maks 2MB (Bakal tersimpan di storage)</div>
   </div>
   <div class="row sb" style="font-size:12px;">
     <span>Status Pembayaran:</span>
     ${pbd('pr')}
   </div>
 </div>`)}</div>

 <div class="col" style="margin-top:14px">${btn('Lacak Status Order (P6)','p',true,'','P6')}${btn('Kembali ke Katalog','s',true,'','P1')}</div></div>`,'dapurthsant.id/order/berhasil')});

add({id:'P6',g:'Pelanggan',t:'Status Order',h:'Lacak Status Order dengan Nomor Referensi',route:'/status',fr:'FR05',
 an:[['FR05','Masukkan nomor referensi lalu Cari.'],['FR05 · ERD','Status progres dapur (status) dan status pembayaran (payment_status).'],['FR05','Rincian item order dan ringkasan tagihan.']],
 r:m=>cust(m,'s',`<div class="h1" style="margin-bottom:10px">Lacak Order</div>${an(1,`<div class="row" style="max-width:560px"><div class="inp" style="flex:1">DST-260924-0012</div>${btn('Cari','p')}</div>`)}<div style="height:16px"></div>
 <div class="split" style="--cols:1.3fr 1fr">${an(2,`<div class="card"><div class="row sb"><b>DST-260924-0012</b>${bd('dp')}</div>${hs('dp')}
 <div class="sumrow" style="margin-top:10px;"><span>Verifikasi Pembayaran:</span>${pbd('vf')}</div>
 <div class="mute sm" style="margin-top:6px;">Diperbarui 24 Sep 2026, 10.42 WIB</div></div>`)}
 ${an(3,`<div class="card"><div class="h2" style="margin-bottom:6px">Rincian</div><div class="sumrow"><span>Dimsum Ayam ×2</span><span>${rp(50000)}</span></div><div class="sumrow"><span>Siomay Udang ×1</span><span>${rp(28000)}</span></div><div class="sumrow"><span>Lumpia ×1</span><span>${rp(22000)}</span></div><div class="sumrow t"><span>Total</span><span>${rp(90000)}</span></div></div>`)}</div>`,'dapurthsant.id/status')});

add({id:'P7',g:'Pelanggan',t:'Kontak UMKM',h:'Kontak: WhatsApp, Alamat, Jam Operasional, Media Sosial',route:'/kontak',fr:'FR06',
 an:[['FR06','Tap-to-chat WhatsApp dan tap-to-call.'],['FR06','Alamat dan tautan media sosial.'],['FR06','Peta dan jam operasional. Data berasal dari halaman Kelola Kontak milik Admin.']],
 r:m=>cust(m,'o',`<div class="h1" style="margin-bottom:10px">Hubungi Kami</div><div class="split" style="--cols:1fr 1fr"><div class="col">
 ${an(1,`<div class="col">${btn('Chat WhatsApp','p',true)}${btn('Telepon','s',true)}</div>`)}
 ${an(2,`<div class="col"><div class="card"><div class="lbl">Alamat</div>Jl. Cikareo Legok, Desa Alamendah, Kec. Rancabali, Kab. Bandung</div><div class="card"><div class="lbl">Media sosial</div><div class="row"><span class="chip">Instagram</span><span class="chip">Facebook</span><span class="chip">TikTok</span></div></div></div>`)}</div>
 ${an(3,`<div class="col">${ph(m?150:200,'Peta lokasi')}<div class="card"><div class="lbl">Jam operasional</div>${jamRows.map(x=>`<div class="sumrow"><span>${x[0]}</span><span>${x[1]}</span></div>`).join('')}</div></div>`)}</div>`,'dapurthsant.id/kontak')});

add({id:'A1',g:'Admin UMKM',t:'Login Admin',h:'Login Admin: Autentikasi Aman',route:'/admin/login',fr:'FR07 · NFR03',
 an:[['FR07','Form kredensial Admin. Setelah berhasil masuk, pengguna menuju Dashboard.'],['NFR03','Pesan error tidak menyebut mana yang salah. Password disimpan ter-hash (Bcrypt/Argon2).']],
 r:m=>login(m,'Admin','admin@dapurthsant.id','dapurthsant.id/admin/login','A2')});

add({id:'A2',g:'Admin UMKM',t:'Dashboard Admin',h:'Dashboard: Ringkasan Cepat Toko',route:'/admin',fr:'FR07',
 an:[['FR07','Kartu ringkasan: order hari ini, menunggu konfirmasi, produk aktif, promo aktif.'],['FR05','Order terbaru dengan indikator status order & status bayar.'],['FR08','Peringatan stok menipis.']],
 r:m=>adm(m,AM,'A2','Dashboard',`${an(1,`<div class="grid gs">${stat('Order hari ini','12','+3 dari kemarin')}${stat('Menunggu konfirmasi','5','perlu verifikasi')}${stat('Produk aktif','18','2 stok menipis')}${stat('Promo aktif','2','1 berakhir minggu ini')}</div>`)}<div style="height:14px"></div>
 <div class="split" style="--cols:1.7fr 1fr"><div><div class="row sb" style="margin-bottom:8px"><span class="h2">Order terbaru</span>${btn('Lihat semua','s','','xs','A4')}</div>${an(2,tbl(['Referensi','Pemesan','Metode','Total','Status Order','Aksi'],OR.slice(0,4).map(o=>[o[0],o[1],o[4],o[2],bd(o[3]),btn('Lihat','s','','xs','A5')]),m))}</div>
 <div style="align-self:start">${an(3,`<div class="card"><div class="h2" style="margin-bottom:6px">Stok menipis</div>${[['Hakau Udang','9'],['Paket Keluarga','5'],['Dimsum Frozen','7']].map(x=>`<div class="sumrow"><span>${x[0]}</span><span class="badge b-amber">sisa ${x[1]}</span></div>`).join('')}</div>`)}</div></div>`,'dapurthsant.id/admin')});

const prodRows=PR.slice(0,5).map(p=>[`<span class="row">${th()}${p.n}</span>`,p.k,rp(p.h),p.s.replace('Stok ',''),tg(p.s!=='Stok habis'),`<span class="row">${btn('Edit','s','','xs','A3b')}<span class="btn dg xs">Nonaktifkan</span></span>`]);
add({id:'A3a',g:'Admin UMKM',t:'Kelola Produk & Kategori (Daftar)',h:'Kelola Produk & Kategori: Daftar dengan Aksi Cepat',route:'/admin/produk',fr:'FR08',
 an:[['FR08','Kategori: filter dan tambah kategori.'],['FR08','Tabel produk dengan toggle aktif.'],['FR08','Tambah produk baru.']],
 r:m=>adm(m,AM,'A3a','Produk & Kategori',`<div class="row sb" style="margin-bottom:10px;flex-wrap:wrap">${an(1,`<div class="pill" style="margin:0">${['Semua','Kukus','Goreng','Frozen','Paket'].map((c,i)=>chip(c,!i)).join('')}<span class="chip" data-go="A3c">+ Kategori</span></div>`)}${an(3,btn('+ Tambah Produk','p','','xs','A3b'))}</div>
 <div class="search" style="margin-bottom:12px;max-width:380px">⌕ Cari produk…</div>${an(2,tbl(['Produk','Kategori','Harga','Stok','Aktif',''],prodRows,m))}`,'dapurthsant.id/admin/produk')});

add({id:'A3b',g:'Admin UMKM',t:'Kelola Produk (Form)',h:'Form Produk: Tambah atau Ubah, dengan Pratinjau Katalog',route:'/admin/produk/baru',fr:'FR08',
 an:[['FR08','Form produk (name, price, stock, description, image_url).'],['FR08','Toggle aktif (is_active).'],['FR01','Pratinjau tampilan di katalog.']],
 r:m=>adm(m,AM,'A3a','Tambah Produk',`<div class="split" style="--cols:1.4fr 1fr">${an(1,`<div class="card">${inp('Nama produk','Dimsum Ayam')}<div class="split" style="--cols:1fr 1fr;gap:10px">${inp('Kategori','Kukus','sel')}${inp('Harga (Rp)','25000')}</div>${inp('Stok','24')}
 <div class="fld"><div class="lbl">Foto produk</div><div class="dz">Tarik foto ke sini atau pilih file<br><span class="sm">JPG/PNG, maks 2 MB.</span></div></div>${inp('Deskripsi','Isi ayam cincang, dikukus segar.','ta')}${an(2,`<div class="row sb" style="margin:6px 0 14px"><span>Tampilkan di katalog</span>${tg(true)}</div>`)}<div class="row" style="justify-content:flex-end">${btn('Batal','s','','','A3a')}${btn('Simpan produk','p','','','A3a')}</div></div>`)}
 <div style="align-self:start">${an(3,`<div><div class="lbl mute">Pratinjau di katalog</div>${prodCard(PR[0],false)}</div>`)}</div></div>`,'dapurthsant.id/admin/produk/baru')});

add({id:'A4',g:'Admin UMKM',t:'Daftar Order Masuk',h:'Daftar Order: Filter Berdasarkan Status',route:'/admin/order',fr:'FR05 · US-002',
 an:[['US-002','Filter status order.'],['FR05 · ERD','Daftar pesanan menampilkan metode bayar dan status pembayaran bukti transfer.']],
 r:m=>adm(m,AM,'A4','Order Masuk',`${an(1,`<div class="chips" style="flex-wrap:${m?'nowrap':'wrap'}">${[['Semua','24'],['Menunggu Konfirmasi','5'],['Diproses','4'],['Dikirim','3'],['Selesai','11'],['Dibatalkan','1']].map((c,i)=>chip(`${c[0]} (${c[1]})`,!i)).join('')}</div>`)}
 <div class="search" style="margin-bottom:12px;max-width:380px">⌕ Cari nomor referensi / nama…</div>${an(2,tbl(['Referensi','Pemesan','Metode','Total','Status Order','Status Bayar',''],OR.map(o=>[o[0],o[1],o[4],o[2],bd(o[3]),pbd(o[5]),btn('Detail','s','','xs','A5')]),m))}`,'dapurthsant.id/admin/order')});

/* A5: Detail Order Ditambahkan Panel Verifikasi Bukti Bayar (ERD: payments) */
add({id:'A5',g:'Admin UMKM',t:'Detail Order & Verifikasi Bayar',h:'Detail Order: Data, Bukti Transfer & Status',route:'/admin/order/{ref}',fr:'FR05 · US-002 · ERD-Payments',
 an:[
   ['FR05','Data pemesan & rincian pesanan.'],
   ['ERD Payments','Panel verifikasi bukti transfer (receipt_image_path, amount_paid, admin_notes).'],
   ['US-002 · ERD','Aksi verifikasi bayar (VERIFIED/REJECTED) dan update tahapan proses dapur (status).']
 ],
 r:m=>adm(m,AM,'A4','Order DST-260924-0012',`<div class="split" style="--cols:1.4fr 1fr"><div class="col">
 ${an(1,`<div class="card"><div class="h2" style="margin-bottom:6px">Data pemesan</div><div class="sumrow"><span class="mute">Nama</span><span>Rina Aulia</span></div><div class="sumrow"><span class="mute">WhatsApp</span><span>0812-xxxx-xxxx</span></div><div class="sumrow"><span class="mute">Alamat</span><span>Jl. Cikareo Legok, Alamendah</span></div><div class="sumrow"><span class="mute">Metode Bayar</span><b>TRANSFER_BANK</b></div></div>`)}

 <div class="card"><div class="h2" style="margin-bottom:6px">Item pesanan (order_items)</div>${tbl(['Produk','Jml','Subtotal'],[['Dimsum Ayam','2',rp(50000)],['Siomay Udang','1',rp(28000)],['Lumpia','1',rp(22000)]],false)}<div class="sumrow"><span>Diskon Promo</span><span>− ${rp(10000)}</span></div><div class="sumrow t"><span>Total Akhir</span><span>${rp(90000)}</span></div></div>

 ${an(2,`<div class="card" style="border:1.5px solid #cbd5e1;">
   <div class="row sb"><span class="h2">Verifikasi Bukti Transfer (tabel payments)</span>${pbd('pr')}</div>
   <div style="font-size:12px;margin:8px 0;">Jumlah Ditagihkan: <b>${rp(90000)}</b></div>
   <div class="lbl">Foto Struk (receipt_image_path):</div>
   <div class="ph" style="height:140px;margin-bottom:10px;">📄 [Preview Gambar: Struk Transfer BCA Rp 90.000 atas nama Rina Aulia]</div>
   ${inp('Catatan Admin (admin_notes)','Bukti transfer valid dan dana masuk ke rekening.')}
   <div class="row" style="gap:8px;margin-top:10px;">
     <span class="btn p xs" style="background:#1E9E5A;border-color:#1E9E5A;color:#fff;">✓ Setujui (VERIFIED)</span>
     <span class="btn dg xs">✕ Tolak (REJECTED)</span>
   </div>
 </div>`)}
 </div>

 <div class="col">${an(3,`<div class="card"><div class="row sb"><span class="h2">Status Tahapan Dapur</span>${bd('mk')}</div>${hs('mk')}${inp('Ubah status dapur ke','Diproses','sel')}${btn('Simpan Perubahan Status','p',true,'','',)}</div>`)}
 <div class="card"><div class="h2" style="margin-bottom:6px">Riwayat Waktu (Timestamps)</div><div class="sumrow"><span>Order Dibuat (created_at)</span><span class="mute">24 Sep, 09.58 WIB</span></div><div class="sumrow"><span>Verifikasi Bayar (verified_at)</span><span class="mute">—</span></div></div></div></div>`,'dapurthsant.id/admin/order/DST-260924-0012')});

add({id:'A6',g:'Admin UMKM',t:'Kelola Kode Promo',h:'Kelola Kode Promo: Buat, Aktifkan, Nonaktifkan',route:'/admin/promo',fr:'FR12 · US-005',
 an:[['FR12','Daftar promo dengan toggle aktif.'],['US-005','Form promo baru (code, discount_type, discount_amount, min_order_amount, valid_until).']],
 r:m=>adm(m,AM,'A6','Kode Promo',`<div class="split" style="--cols:1.5fr 1fr">${an(1,`<div>${tbl(['Kode','Tipe','Nilai','Periode','Aktif',''],[['HEMAT10','FIXED',rp(10000),'1–30 Sep',tg(true),`<span class="row"><span class="btn s xs">Edit</span><span class="btn dg xs">Hapus</span></span>`],['DIMSUM15','PERCENT','15%','15 Sep–15 Okt',tg(true),`<span class="row"><span class="btn s xs">Edit</span><span class="btn dg xs">Hapus</span></span>`],['LEBARAN','PERCENT','20%','Berakhir',tg(false),`<span class="row"><span class="btn s xs">Edit</span><span class="btn dg xs">Hapus</span></span>`]],m)}</div>`)}
 <div style="align-self:start">${an(2,`<div class="card"><div class="h2" style="margin-bottom:8px">Promo baru</div>${inp('Kode promo (code)','PROMO25')}<div class="fld"><div class="lbl">Tipe (discount_type)</div><div class="row"><span><i class="ck on"></i>PERCENT</span><span><i class="ck"></i>FIXED</span></div></div>${inp('Nilai diskon (discount_amount)','25')}${inp('Minimal order (min_order_amount)','50000')}<div class="split" style="--cols:1fr 1fr;gap:8px">${inp('Mulai','01/10/2026')}${inp('Berakhir (valid_until)','31/10/2026')}</div><div class="row sb" style="margin-bottom:12px"><span>Aktif (is_active)</span>${tg(true)}</div>${btn('Simpan promo','p',true)}</div>`)}</div></div>`,'dapurthsant.id/admin/promo')});

add({id:'A7',g:'Admin UMKM',t:'Kelola Informasi Kontak',h:'Kelola Kontak: WhatsApp, Alamat, Jam Operasional',route:'/admin/kontak',fr:'FR06',
 an:[['FR06','Form kontak outlet.'],['FR06','Pratinjau tampilan ke pelanggan.']],
 r:m=>adm(m,AM,'A7','Informasi Kontak',`<div class="split" style="--cols:1.4fr 1fr">${an(1,`<div class="card">${inp('Nomor WhatsApp','0812-xxxx-xxxx')}${inp('Alamat','Jl. Cikareo Legok, Desa Alamendah, Kec. Rancabali','ta')}
 <div class="fld"><div class="lbl">Jam operasional</div>${[['Senin – Jumat','08.00','17.00'],['Sabtu','08.00','15.00'],['Minggu','Tutup','']].map(x=>`<div class="row" style="margin-bottom:6px"><span style="width:${m?80:120}px;flex:none">${x[0]}</span><div class="inp" style="flex:1;min-height:32px;padding:5px 8px">${x[1]}</div><div class="inp" style="flex:1;min-height:32px;padding:5px 8px">${x[2]}</div></div>`).join('')}</div>
 ${inp('Instagram','@dapurthsant')}${inp('Facebook','fb.com/dapurthsant')}${inp('TikTok','@dapurthsant')}<div class="row" style="justify-content:flex-end">${btn('Simpan perubahan','p')}</div></div>`)}
 <div style="align-self:start">${an(2,`<div class="card soft"><div class="lbl mute">Pratinjau halaman Kontak</div>${btn('Chat WhatsApp','p',true)}<div class="line" style="margin-top:12px"></div><div class="line"></div><div class="line" style="width:60%"></div></div>`)}</div></div>`,'dapurthsant.id/admin/kontak')});

add({id:'O1',g:'Owner / Pemilik',t:'Login Owner',h:'Login Owner: Masuk ke Mode Monitoring',route:'/owner/login',fr:'FR07',
 an:[['FR07','Form login Owner (role: OWNER).'],['NFR03','Hak akses read-only.']],
 r:m=>login(m,'Owner','owner@dapurthsant.id','dapurthsant.id/owner/login','O2')});

add({id:'O2',g:'Owner / Pemilik',t:'Dashboard Ringkasan',h:'Dashboard Owner: Ringkasan Order dan Produk',route:'/owner',fr:'US-003',
 an:[['US-003','Penanda read-only.'],['US-003','Kartu ringkasan omset & transaksi.'],['US-003','Grafik sebaran status order.']],
 r:m=>adm(m,OM,'O2','Dashboard Ringkasan',`${an(1,`<div class="ro">Mode monitoring: hanya baca</div>`)}${an(2,`<div class="grid gs">${stat('Total order','124','bulan ini')}${stat('Order selesai','96','77%')}${stat('Produk aktif','18','dari 20 produk')}${stat('Order diproses','8','sedang berjalan')}</div>`)}<div style="height:14px"></div>
 ${an(3,`<div class="split" style="--cols:1fr 1fr"><div class="card"><div class="h2" style="margin-bottom:8px">Order per status</div>${[['Menunggu','5',20],['Diproses','8',32],['Dikirim','3',12],['Selesai','96',100],['Dibatalkan','2',8]].map(x=>`<div class="hb"><span>${x[0]}</span><div><div class="hbar" style="width:${x[2]}%"></div></div><b>${x[1]}</b></div>`).join('')}</div>
 <div class="card"><div class="h2" style="margin-bottom:8px">Order 7 hari terakhir</div><div class="vbars">${[40,55,35,70,60,90,75].map(v=>`<i style="height:${v}%"></i>`).join('')}</div><div class="row sb sm mute" style="margin-top:4px"><span>Sen</span><span>Sel</span><span>Rab</span><span>Kam</span><span>Jum</span><span>Sab</span><span>Min</span></div></div></div>`)}`,'dapurthsant.id/owner','Owner')});

add({id:'O3',g:'Owner / Pemilik',t:'Laporan Produk & Order',h:'Laporan Produk & Order: Hanya Baca',route:'/owner/laporan',fr:'US-003',
 an:[['US-003','Filter periode omset.'],['US-003','Tabel laporan terintegrasi data pesanan lunas.']],
 r:m=>adm(m,OM,'O3','Laporan',`<div class="ro">Mode monitoring: hanya baca</div>${an(1,`<div class="row sb" style="margin-bottom:12px;flex-wrap:wrap"><div class="row"><span class="chip on">Order</span><span class="chip">Produk</span></div><div class="row"><span class="chip">7 hari</span><span class="chip on">30 hari</span><span class="chip">Custom</span></div></div>`)}
 ${an(2,tbl(['Referensi','Tanggal','Pemesan','Metode','Total','Status Order'],OR.map(o=>[o[0],'24 Sep 2026',o[1],o[4],o[2],bd(o[3])]),m))}`,'dapurthsant.id/owner/laporan','Owner')});

/* Design system */
const sw=(n,h,dark)=>`<div class="sw"><i style="background:${h}"></i><div><b>${n}</b>${h}</div></div>`;
function ds(){
 return `<div class="dsx">
 <div class="h1" style="margin-bottom:4px">Design System &amp; Tokens</div><div class="mute" style="margin-bottom:18px">Token visual disesuaikan dengan identitas brand Dapur Th Sant dan skema database ERD.</div>
 <h3>Palet Hi-Fi Polished (Terracotta & Slate)</h3><div class="dsg">${sw('Terracotta (Primary)','#B5451A')}${sw('Cream (Latar Pelanggan)','#FDF3E8')}${sw('Surface Card','#FFFFFF')}${sw('Slate (Teks)','#2B2118')}${sw('Garis Halus','#E6E1DC')}${sw('Latar Admin (Lavender-Abu)','#F4F4FA')}</div>
 <h3>Status Enum di Database (ERD)</h3>
 <div class="card" style="margin-bottom:12px;">
   <div class="lbl">orders.status</div>
   <div class="row" style="flex-wrap:wrap;margin:4px 0 10px;">${Object.keys(ST).map(bd).join('')}</div>
   <div class="lbl">payments.payment_status</div>
   <div class="row" style="flex-wrap:wrap;margin:4px 0;">${Object.keys(PST).map(pbd).join('')}</div>
 </div>
 <h3>Breakpoint</h3><div class="card"><div class="sumrow"><span>Seluler</span><b>360–414 px · grid katalog 2 kolom · menu bawah</b></div><div class="sumrow"><span>Desktop</span><b>1366–1920 px · grid katalog 4 kolom · header atas / sidebar</b></div></div>
 <h3>Alur Navigasi</h3>${flowHTML()}
 <h3>Matriks Keterlacakan Kebutuhan &amp; ERD</h3>${traceHTML()}
 </div>`;
}
add({id:'DS',g:'Sistem',t:'Design System & Tokens',h:'Design System: Palet, Tipografi, Komponen, Breakpoint',route:'—',fr:'NFR02',an:[],r:null});

add({id:'P3b',g:'Pelanggan',t:'Cart Kosong (State)',h:'State Cart Kosong: Ajakan Kembali ke Katalog',route:'/cart',fr:'FR03',
 an:[['FR03','Cart kosong.'],['FR04','Tombol Checkout nonaktif.']],
 r:m=>{CARTN=0;const h=cust(m,'c',`<div class="h1" style="margin-bottom:10px">Cart Anda</div><div class="split" style="--cols:1.6fr 1fr">${an(1,`<div class="card" style="text-align:center;padding:40px 16px"><div class="okbig" style="border-color:var(--w-line);color:var(--w-mute)">🛒</div><div class="h2">Cart masih kosong</div><div class="mute" style="margin:6px 0 14px">Yuk pilih dimsum favorit Anda di katalog.</div>${btn('Lihat katalog','p','','','P1')}</div>`)}
 <div style="align-self:start">${an(2,`<div class="card soft"><div class="h2" style="margin-bottom:8px">Ringkasan</div><div class="sumrow"><span>Subtotal</span><span>${rp(0)}</span></div><div class="sumrow t"><span>Total</span><span>${rp(0)}</span></div><div style="margin-top:12px">${btn('Lanjut ke Checkout','p',true,'off')}</div></div>`)}</div></div>`,'dapurthsant.id/cart');CARTN=2;return h}});

add({id:'P6b',g:'Pelanggan',t:'Status Tidak Ditemukan (State)',h:'State Nomor Referensi Tidak Ditemukan',route:'/status',fr:'FR05',
 an:[['FR05','Kolom error merah.'],['FR05','Pesan panduan koreksi.']],
 r:m=>cust(m,'s',`<div class="h1" style="margin-bottom:10px">Lacak Order</div>${an(1,`<div class="row" style="max-width:560px"><div class="inp" style="flex:1;border-color:#dc2626">DST-000000-0000</div>${btn('Cari','p')}</div>`)}<div style="height:16px"></div>
 ${an(2,`<div class="card" style="max-width:560px;border-color:#fecaca;background:#fef2f2"><div class="h2" style="color:#b91c1c">Nomor referensi tidak ditemukan</div><div class="mute" style="margin:6px 0 10px">Periksa lagi formatnya: DST-260924-0012.</div>${btn('Hubungi UMKM','s','','xs','P7')}</div>`)}`,'dapurthsant.id/status')});

add({id:'A3c',g:'Admin UMKM',t:'Kelola Kategori',h:'Kelola Kategori: Tambah, Ubah, dan Hapus Kategori',route:'/admin/kategori',fr:'FR08',
 an:[['FR08','Daftar kategori.'],['FR08','Form kategori baru.']],
 r:m=>adm(m,AM,'A3a','Kategori Produk',`<div class="split" style="--cols:1.5fr 1fr">${an(1,`<div>${tbl(['Kategori','Jumlah produk','Aktif',''],[['Kukus','8',tg(true),`<span class="row"><span class="btn s xs">Edit</span><span class="btn s xs off">Hapus</span></span>`],['Goreng','4',tg(true),`<span class="row"><span class="btn s xs">Edit</span><span class="btn s xs off">Hapus</span></span>`],['Frozen','3',tg(true),`<span class="row"><span class="btn s xs">Edit</span><span class="btn s xs off">Hapus</span></span>`],['Musiman','0',tg(false),`<span class="row"><span class="btn s xs">Edit</span><span class="btn dg xs">Hapus</span></span>`]],m)}</div>`)}
 <div style="align-self:start">${an(2,`<div class="card"><div class="h2" style="margin-bottom:8px">Kategori baru</div>${inp('Nama kategori','Paket Hemat')}<div class="row sb" style="margin-bottom:12px"><span>Aktif</span>${tg(true)}</div>${btn('Simpan kategori','p',true)}</div>`)}</div></div>`,'dapurthsant.id/admin/kategori')});

const ORDER=['P1','P2','P3','P3b','P4','P5','P6','P6b','P7','A1','A2','A3a','A3b','A3c','A4','A5','A6','A7','O1','O2','O3','DS'];
SC.sort((a,b)=>ORDER.indexOf(a.id)-ORDER.indexOf(b.id));
const FLW={'Pelanggan':[['Katalog','Detail Produk','Cart','Checkout (Pilih Bank/COD)','Order Sukses & Upload Bukti','Lacak Status']],
 'Admin UMKM':[['Login Admin','Dashboard','Pesanan Masuk','Review Bukti Transfer (Setujui/Tolak)','Update Dapur (Diproses/Dikirim)']],
 'Owner':[['Login Owner','Dashboard Ringkasan Omset','Laporan Order Lunas (Hanya Baca)']]};
const flowHTML=()=>Object.keys(FLW).map(k=>`<div class="card" style="margin-bottom:10px"><div class="lbl">${k}</div>${FLW[k].map(l=>`<div class="row" style="flex-wrap:wrap;margin:6px 0">${l.map(n=>`<span class="chip">${n.replace(/&/g,'&amp;')}</span>`).join('<span class="mute">→</span>')}</div>`).join('')}</div>`).join('');
const TR=[
  ['FR01','Katalog, pencarian, filter kategori','P1'],
  ['FR02','Detail produk','P2, A3b'],
  ['FR03','Cart: stepper jumlah, hapus item','P3, P3b'],
  ['FR04 / ERD Orders','Checkout & pembuatan order (payment_method: TRANSFER_BANK/COD)','P4'],
  ['ERD Payments','Upload bukti transfer & verifikasi (receipt_image_path, payment_status)','P5, A5'],
  ['FR05','Pelacakan status order & status bayar','P6, P6b, A4, A5'],
  ['FR06','Kontak outlet & integrasi jam operasional','P7, A7'],
  ['FR07','Login admin & owner','A1, O1'],
  ['FR08','Kelola produk dan kategori','A3a, A3b, A3c'],
  ['FR11 · FR12','Sistem kode promo (promos table)','P4, A6'],
  ['NFR02','Responsif adaptif 1366px desktop & 360-414px seluler','Semua Layar']
];
const traceHTML=()=>tbl(['Kode / Aspek','Deskripsi Kebutuhan & Integrasi ERD','Layar Terkait'],TR,false);

/* ---------- singkatan anotasi ---------- */
const SHORT={
 P1:['Pencarian Produk','Filter Kategori','Grid Produk','Stok Habis'],
 P2:['Galeri Foto','Info Produk','Tambah ke Cart','Stok Habis'],
 P3:['Daftar Item','Ringkasan Total','Tombol Checkout'],
 P4:['Data Pemesan','Metode Bayar','Kode Promo','Ringkasan Tagihan'],
 P5:['Nomor Referensi','Upload Bukti Transfer','Status PENDING_REVIEW'],
 P6:['Cari Referensi','Progres Dapur & Bayar','Rincian Order'],
 P7:['Tap-to-Chat','Alamat & Medsos','Peta & Jam Buka'],
 A1:['Form Login','Keamanan Login'],
 A2:['Kartu Ringkasan','Order Terbaru','Stok Menipis'],
 A3a:['Kategori','Tabel Produk','Tambah'],
 A3b:['Form Produk','Status Aktif','Pratinjau Katalog'],
 A4:['Filter Status','Tabel Order & Status Bayar'],
 P3b:['Empty State','Checkout Nonaktif'],P6b:['Input Error','Pesan Error'],A3c:['Daftar Kategori','Form Kategori'],
 A5:['Data Pemesan','Verifikasi Bukti Transfer','Update Status Dapur'],
 A6:['Daftar Promo','Form Promo'],
 A7:['Form Kontak','Pratinjau Kontak'],
 O1:['Form Login','Akses Baca Saja'],
 O2:['Mode Monitoring','Kartu Ringkasan','Grafik Order'],
 O3:['Filter Laporan','Tabel Read-only']};
const SUB={P3b:'Cart Kosong',P6b:'Tidak Ditemukan',A3c:'Kategori',P1:'Katalog',P2:'Detail',P3:'Cart',P4:'Checkout',P5:'Bukti Bayar',P6:'Status',P7:'Kontak',A1:'Login',A2:'Dashboard',A3a:'Produk',A3b:'Form Produk',A4:'Order',A5:'Verifikasi Bayar',A6:'Promo',A7:'Kontak',O1:'Login',O2:'Dashboard',O3:'Laporan'};
SC.forEach(s=>{if(SHORT[s.id])s.an=s.an.map((a,i)=>[a[0],SHORT[s.id][i]||'Catatan',a[1]])});
const GROUPS=[['Pelanggan','1. Web Pelanggan: Katalog & Order','🌐'],['Admin UMKM','2. Panel Admin UMKM','🛠️'],['Owner / Pemilik','3. Dashboard Owner','📊'],['Sistem','4. Design System & Tokens','🎨']];

/* ---------- state & render ---------- */
let cur='P1',mode='hi',dev='d',mw=390,anno=true;
const $=id=>document.getElementById(id);
const byId=Object.fromEntries(SC.map(s=>[s.id,s]));
const esc=t=>t.replace(/&/g,'&amp;');
function toast(t){const e=$('toast');e.textContent=t;e.classList.add('on');clearTimeout(toast.t);toast.t=setTimeout(()=>e.classList.remove('on'),1800)}
const tabs=$('tabs');
GROUPS.forEach(g=>{const b=document.createElement('button');b.setAttribute('role','tab');b.dataset.g=g[0];b.innerHTML=`${g[2]} ${esc(g[1])}`;tabs.appendChild(b)});
function render(){
 $('scl').textContent='';
 const s=byId[cur];AN=s.an;
 tabs.querySelectorAll('button').forEach(b=>b.setAttribute('aria-selected',b.dataset.g===s.g));
 const sibs=SC.filter(x=>x.g===s.g);
 $('info').innerHTML=`<div class="trow"><span class="sbadge">S1-${s.id} Wireframe</span><h2>${esc(s.h)}</h2></div>
   <p>${s.id==='DS'?'Kumpulan token visual, komponen, dan relasi tabel ERD yang dipakai di seluruh sistem.':`Diimplementasikan oleh <b>Tim SATURN (Frontend)</b> sebagai antarmuka web responsif pada rute <code>${s.route}</code> · Acuan <b>${s.fr}</b> · Aktor <b>${esc(s.g)}</b>`}</p>
   ${sibs.length>1?`<div class="subs">${sibs.map(x=>`<button data-go="${x.id}" aria-pressed="${x.id===cur}">${x.id} ${SUB[x.id]}</button>`).join('')}</div>`:''}`;
 const hi=mode==='hi'?' hi':'';
 const st=$('stage'),isDS=s.id==='DS';
 $('devbar').style.display=isDS?'none':'flex';$('mw').hidden=dev!=='m';
 $('modet').textContent='Mode: '+(mode==='hi'?'Hi-Fi Polished':'Mid-Fi Wireframe');$('annot').textContent=(anno?'Sembunyikan':'Tampilkan')+' Anotasi FR/US';
 st.className='stagein'+(anno&&!isDS?' pinon':'');
 if(isDS){st.innerHTML=`<div class="fr free${hi}" style="max-width:1200px">${ds()}</div>`}
 else if(dev==='m'){st.innerHTML=`<div class="mwrap"><div class="fr m${hi}" style="width:${mw}px">${s.r(true)}</div></div>`}
 else{st.innerHTML=`<div class="dscale"><div class="fr d${hi}">${s.r(false)}</div></div>`}
 const nt=$('notes');
 if(anno&&!isDS&&s.an.length){nt.hidden=false;nt.innerHTML=`<h3>Catatan Anotasi Kebutuhan &amp; ERD</h3><div class="ng">${s.an.map(a=>`<div class="ni"><span class="ptag">${a[0]}</span><b>${a[1]}</b><br>${a[2]}</div>`).join('')}</div>`}else nt.hidden=true;
 fit();
}
function fit(){document.querySelectorAll('.dscale').forEach(w=>{const f=w.firstElementChild;f.style.transform='none';const k=Math.min(1,w.clientWidth/1366);f.style.transform=`scale(${k})`;$('scl').textContent=k<1?`Ditampilkan ${Math.round(k*100)}% dari ukuran asli 1366px`:'';w.style.height=(f.offsetHeight*k)+'px'})}
function go(id){if(!byId[id])return;cur=id;try{history.replaceState(null,'','#'+id)}catch(e){}render()}

tabs.addEventListener('click',e=>{const b=e.target.closest('button');if(!b)return;const f=SC.find(x=>x.g===b.dataset.g);go(f.id)});
$('modeb').addEventListener('click',()=>{mode=mode==='hi'?'lo':'hi';render()});
$('annob').addEventListener('click',()=>{anno=!anno;render()});$('dev').addEventListener('click',e=>{const b=e.target.closest('button');if(!b)return;dev=b.dataset.v;document.querySelectorAll('#dev button').forEach(x=>x.setAttribute('aria-pressed',x===b));render()});
$('mw').addEventListener('click',e=>{const b=e.target.closest('button');if(!b)return;mw=+b.dataset.v;document.querySelectorAll('#mw button').forEach(x=>x.setAttribute('aria-pressed',x===b));render()});
$('info').addEventListener('click',e=>{const b=e.target.closest('[data-go]');if(b)go(b.dataset.go)});

$('stage').addEventListener('click',e=>{
 const g=e.target.closest('[data-go]');
 const q=e.target.closest('.qty b');
 if(q){const i=q.parentElement.querySelector('i');let n=+i.textContent;n=q.textContent==='+'?n+1:Math.max(1,n-1);i.textContent=n;return}
 const t=e.target.closest('.tg');if(t){t.classList.toggle('on');toast('Status diubah (simulasi)');return}
 const c=e.target.closest('.chip');if(c&&!c.dataset.go){c.parentElement.querySelectorAll('.chip').forEach(x=>x.classList.remove('on'));c.classList.add('on');toast('Filter: '+c.textContent.trim());return}
 const dt=e.target.closest('[data-toast]');if(dt){toast(dt.dataset.toast);return}
 const of=e.target.closest('.btn.off');if(of){toast('Tombol nonaktif pada kondisi ini');return}
 if(g){const id=g.dataset.go;toast('Menuju layar '+id);go(id);window.scrollTo({top:0});return}
 const b=e.target.closest('.btn');if(b)toast(b.textContent.trim()+': aksi disimulasikan');
});
window.addEventListener('resize',fit);window.addEventListener('load',fit);
const h=(location.hash||'').slice(1);if(byId[h])cur=h;
render();
</script>
</body>
</html>
