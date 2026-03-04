$htmlFile = 'mobile-catering-van-insurance.html'
$cssFile  = 'styles.css'

# ── 1. Append CSS classes to styles.css ──────────────────────────────────────
$newCss = @'

/* --- MCV: Global icon primary colour (sections only, not hero/nav/footer) --- */
.mcv-page section:not(.bt-hero) i.fas,
.mcv-page section:not(.bt-hero) i.far,
.mcv-page section:not(.bt-hero) i.fab {
  color: #C65100;
}

/* --- MCV: Badge bar (below hero) --- */
.mcv-badge-bar { background: #003672; padding: 18px 0; }
.mcv-badge-bar span { color: #fff; font-weight: 700; font-size: 1rem; }
.mcv-badge-bar i { color: #C65100; margin-right: 0.5rem; }

/* --- MCV: What Is section --- */
.mcv-what-is-img { border-radius: 20px; }
.mcv-what-is p { font-size: 1.1rem; line-height: 1.8; color: #555; }
.mcv-infobox { background: #f8f9fa; border-left: 4px solid #C65100; padding: 1.25rem; border-radius: 6px; }
.mcv-infobox .mcv-infobox-title { font-weight: 700; color: #C65100; font-size: 1.2rem; margin-bottom: 0.5rem; }
.mcv-infobox .mcv-infobox-text { color: #555; font-size: 1.1rem; margin: 0; }

/* --- MCV: Why Specialised section --- */
.mcv-why-bg { background-color: #f8f9fa; }
.mcv-why-intro p { font-size: 1.25rem; color: #555; font-weight: 500; }
.mcv-check-card { border-left: 4px solid #C65100; transition: transform 0.2s; }
.mcv-check-card:hover { transform: translateY(-2px); }
.mcv-check-card i { color: #C65100; font-size: 1.25rem; }
.mcv-check-card span { font-weight: 500; color: #444; font-size: 1.05rem; }
.mcv-highlight-block { background: linear-gradient(135deg, #C65100 0%, #a44300 100%); border-radius: 1rem; padding: 1.5rem; }
.mcv-highlight-block i.fas { color: #fff; font-size: 2.2rem; }
.mcv-highlight-block p { font-size: 18px; line-height: 1.8; color: #fff; }
.mcv-highlight-block .mcv-highlight-divider { border-top: 1px solid rgba(255,255,255,0.2); margin-top: 1rem; padding-top: 1rem; }
.mcv-highlight-block .mcv-highlight-divider i { color: #fff; font-size: 1.5rem; }
.mcv-highlight-block .mcv-highlight-divider p { font-weight: 700; color: #fff; font-size: 18px; letter-spacing: 0.5px; margin: 0; }

/* --- MCV: Coverage cards h4 --- */
.mcv-page .ft-card h4 { color: #1a1a1a; }
.mcv-page .ft-card i { color: #C65100; }
.mcv-card-note { color: #003672; border-left: 3px solid #C65100; padding-left: 12px; font-size: 0.95rem; }
.mcv-bi-card { max-width: 600px; width: 100%; }
.mcv-bi-card i { color: #C65100; font-size: 2rem; }

/* --- MCV: Cost section --- */
.mcv-cost-bg { background: linear-gradient(135deg, #f8f9fa 0%, #eef2f7 100%); }
.mcv-pill { background: #fff; border: 2px solid #e0e8f0; }
.mcv-pill i { color: #C65100; }
.mcv-pill span { font-weight: 600; color: #333; font-size: 0.95rem; }
.mcv-price-left { background: linear-gradient(135deg, #003672 0%, #0055a5 100%); }
.mcv-price-left i { color: rgba(255,255,255,0.4); font-size: 3rem; }
.mcv-price-label { font-size: 1rem; letter-spacing: 1px; text-transform: uppercase; opacity: 0.85; }
.mcv-price-value { font-size: 3.5rem; line-height: 1.1; }
.mcv-price-sub { opacity: 0.8; font-size: 0.9rem; }
.mcv-price-right { background: #fff; }
.mcv-price-right h5 { color: #003672; }
.mcv-price-right h5 span { color: #C65100; }

/* --- MCV: Why Choose Us section --- */
.mcv-trust-bg { background: #fff; }
.mcv-trust-img { object-fit: cover; max-height: 480px; }
.mcv-trust-intro { color: #555; font-size: 1.05rem; line-height: 1.8; }
.mcv-trust-focus { color: #003672; font-size: 1.05rem; }
.mcv-trust-list i { color: #C65100; font-size: 1.1rem; }
.mcv-trust-list span { color: #444; font-size: 1rem; }
.mcv-trust-note { background: #f8f9fa; border-left: 4px solid #C65100; padding: 1rem; border-radius: 6px; }
.mcv-trust-note p { color: #333; font-size: 1rem; line-height: 1.7; margin: 0; }

/* --- MCV: Claims section --- */
.mcv-claim-card { border: 1px solid #e8edf3; box-shadow: 0 2px 12px rgba(0,0,0,0.05); transition: transform 0.2s; }
.mcv-claim-card:hover { transform: translateY(-4px); }
.mcv-claim-icon-wrap { width: 70px; height: 70px; background: #fff4ee; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; }
.mcv-claim-icon-wrap i { color: #C65100; font-size: 1.8rem; }
.mcv-claim-card p { color: #222; font-size: 1rem; margin: 0; font-weight: 700; }
.mcv-claims-note { background: #fff; border-left: 4px solid #C65100; box-shadow: 0 4px 12px rgba(0,0,0,0.06); padding: 1.25rem; border-radius: 6px; }
.mcv-claims-note p { color: #333; font-size: 1rem; line-height: 1.7; margin: 0; }

/* --- MCV: Steps section --- */
.mcv-steps-bg { background: #fff; }
.mcv-step-card { background: #fff4ee; border: 1px solid #ffe0cc; border-radius: 14px; padding: 1.5rem; position: relative; overflow: hidden; height: 100%; }
.mcv-step-num { position: absolute; top: -10px; right: 10px; font-size: 5rem; font-weight: 900; color: rgba(255,107,0,0.1); line-height: 1; user-select: none; }
.mcv-step-icon { width: 52px; height: 52px; background: #C65100; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem; }
.mcv-step-icon i { color: #fff; font-size: 1.3rem; }
.mcv-step-card p { color: #222; font-weight: 600; font-size: 0.97rem; margin: 0; line-height: 1.6; }
'@

Add-Content -Path $cssFile -Value $newCss -Encoding UTF8
Write-Host "CSS appended."
