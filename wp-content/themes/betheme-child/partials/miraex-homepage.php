<?php
/**
 * Miraex homepage content.
 *
 * Header and footer are intentionally excluded. Use BeTheme Header Builder
 * and Footer Builder for those areas.
 *
 * This file is also exposed through [miraex_homepage].
 */
?>
<section class="hero">
  <div class="hero-media"><img src="<?php echo esc_url( miraex_asset_shortcode( array( "path" => "img/hero-photonics.jpg" ) ) ); ?>" alt="A beam of light splitting into a spectrum of cyan and violet — quantum information carried on photons"></div>
  <div class="container wide">
    <div class="hero-inner">
      <div class="parent-strip reveal"><span>Now part of</span> <b>SEALSQ</b> <span class="tag">Quantum Sovereign Stack</span></div>
      <h1 class="reveal d1">The quantum interconnect layer of the <span class="grad-text">sovereign quantum era</span>.</h1>
      <p class="lead reveal d2">Miraex builds thin-film lithium tantalate photonic integrated circuits that convert quantum information between microwave and optical light — the connective tissue linking quantum processors, sensors and networks into one coherent infrastructure.</p>
      <div class="btn-row reveal d3">
        <a class="btn btn-primary btn-lg" href="<?php echo esc_url( miraex_page_url( "technology" ) ); ?>">Explore the platform <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
        <a class="btn btn-ghost btn-lg" href="<?php echo esc_url( miraex_page_url( "contact" ) ); ?>">Request a datasheet</a>
      </div>
      <div class="hero-meta reveal d4">
        <div class="m"><b>MHz–100<span style="font-size:18px">GHz+</span></b><span>conversion bandwidth</span></div>
        <div class="m"><b>10⁴</b><span>microwave↔optical energy gap bridged</span></div>
        <div class="m"><b>3</b><span>quantum verticals</span></div>
        <div class="m"><b>EPFL</b><span>Innovation Park, CH</span></div>
      </div>
    </div>
  </div>
  <div class="scroll-cue"><span>Scroll</span><span class="dot"></span></div>
</section>

<!-- value / area of expertise -->
<section class="section bg-navy">
  <div class="container">
    <div class="section-head center reveal">
      <p class="eyebrow">Our area of expertise</p>
      <h2 class="h2">Connecting quantum resources across<br>frequency, distance and modality</h2>
      <p class="lead mt-s">Photonic integrated circuits deliver a genuine quantum advantage through interconnectivity and entanglement — the foundation distributed sensing, computing and networks are built on.</p>
    </div>
    <div class="grid cols-3">
      <a class="card feature vert-card reveal" href="<?php echo esc_url( miraex_page_url( "distributed-quantum-computing" ) ); ?>">
        <div class="num">01 — Computing</div>
        <div class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="4" width="16" height="16" rx="2"/><rect x="9" y="9" width="6" height="6"/><path d="M9 1v3M15 1v3M9 20v3M15 20v3M1 9h3M1 15h3M20 9h3M20 15h3"/></svg></div>
        <h3>Distributed Quantum Computing</h3>
        <p>Quantum interconnects bridge stationary microwave qubits and flying optical photons, linking distant QPUs into one cluster over telecom fibre.</p>
        <span class="more">Explore <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
      </a>
      <a class="card feature vert-card reveal d1" href="<?php echo esc_url( miraex_page_url( "quantum-sensing" ) ); ?>">
        <div class="num">02 — Sensing</div>
        <div class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 12a0 0 0 100 0"/><circle cx="12" cy="12" r="2"/><path d="M5 12a7 7 0 0114 0M2 12a10 10 0 0120 0"/><path d="M12 14v7"/></svg></div>
        <h3>Quantum Sensing</h3>
        <p>Ultra-low-noise transducers and entanglement-based distributed sensing for precision navigation, geophysics and defence.</p>
        <span class="more">Explore <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
      </a>
      <a class="card feature vert-card reveal d2" href="<?php echo esc_url( miraex_page_url( "quantum-networking" ) ); ?>">
        <div class="num">03 — Networking</div>
        <div class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="5" cy="6" r="2.4"/><circle cx="19" cy="6" r="2.4"/><circle cx="12" cy="18" r="2.4"/><path d="M7 7.4l3.6 8.4M17 7.4l-3.6 8.4M7.2 6h9.6"/></svg></div>
        <h3>Quantum Networking</h3>
        <p>Quantum repeaters that extend entanglement beyond the fibre limit — the backbone of a future, planet-scale quantum internet.</p>
        <span class="more">Explore <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
      </a>
    </div>
  </div>
</section>

<!-- photon journey / how it works -->
<section class="section bg-deep">
  <div class="container">
    <div class="split">
      <div class="reveal">
        <p class="eyebrow">The core capability</p>
        <h2 class="h2" style="margin-bottom:18px">Microwave in. Optical out.<br>A bridge across five orders of magnitude.</h2>
        <p class="lead">The energies of stationary qubits (microwave photons) and flying qubits (optical photons) differ by roughly 10⁴. Miraex's traveling-wave electro-optic transducers convert coherently between the two domains — without which distributed quantum architectures cannot function at scale.</p>
        <ul class="ticks">
          <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l4.5 4.5L19 7"/></svg><span><b>High-efficiency</b> direct electro-optic transduction, MHz to beyond 100 GHz</span></li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l4.5 4.5L19 7"/></svg><span><b>Cryogenic & quantum-device compatible</b> — operates alongside superconducting processors</span></li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l4.5 4.5L19 7"/></svg><span><b>Ultra-low-noise</b> microwave detection to ~hundreds of photons</span></li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l4.5 4.5L19 7"/></svg><span><b>Entanglement</b> generated between the microwave and optical domains</span></li>
        </ul>
        <div class="btn-row" style="margin-top:28px"><a class="btn btn-ghost" href="<?php echo esc_url( miraex_page_url( "technology" ) ); ?>">Inside the TFLT platform <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a></div>
      </div>
      <div class="media-frame glow reveal d1"><img src="<?php echo esc_url( miraex_asset_shortcode( array( "path" => "img/photon-journey.jpg" ) ) ); ?>" alt="Microwave waves entering a photonic chip on the left and emerging as an optical beam on the right"></div>
    </div>
  </div>
</section>

<!-- Root-to-Qubit stack -->
<section class="section bg-navy">
  <div class="container">
    <div class="section-head reveal">
      <p class="eyebrow">Where Miraex sits</p>
      <h2 class="h2">The Root-to-Qubit stack</h2>
      <p class="lead mt-s">SEALSQ assembled a sovereign, end-to-end quantum architecture — from post-quantum silicon to orbit. Miraex closes the layer that makes it whole: the quantum interconnect.</p>
    </div>
    <div class="stack reveal d1">
      <div class="stack-layer"><span class="lyr-n">05</span><div><h4>Orbital infrastructure</h4><p>Quantum-secure links across Low Earth Orbit — the Quantum Orbital Space Cloud (QOSC)</p></div><span class="who">WISeSat · QOSC</span></div>
      <div class="stack-layer"><span class="lyr-n">04</span><div><h4>Quantum networking & QKD</h4><p>Entanglement distribution and key delivery at planetary scale</p></div><span class="who">Quantum internet</span></div>
      <div class="stack-layer is-miraex"><span class="lyr-n">03</span><div><h4>Quantum interconnect — the missing link</h4><p>TFLT PIC microwave-to-optical transduction bridging processors, sensors and networks</p></div><span class="who">Miraex</span></div>
      <div class="stack-layer"><span class="lyr-n">02</span><div><h4>Quantum processors</h4><p>Qubit hardware that the interconnect links into collective compute</p></div><span class="who">EeroQ · ColibriTD</span></div>
      <div class="stack-layer"><span class="lyr-n">01</span><div><h4>Post-quantum silicon</h4><p>Quantum-resistant secure microcontrollers & root of trust</p></div><span class="who">SEALSQ QS7001</span></div>
    </div>
    <p class="note" style="margin-top:18px">Root-to-Qubit is SEALSQ's architecture spanning quantum-resistant semiconductors to orbital infrastructure. Miraex completes the interconnect layer.</p>
  </div>
</section>

<!-- proof / stats -->
<section class="section-sm bg-deep">
  <div class="container">
    <div class="stats">
      <div class="stat reveal"><b data-count="10" data-suffix="">10</b><span>Photonics & quantum specialists</span></div>
      <div class="stat reveal d1"><b><span style="font-size:.6em;letter-spacing:.02em;vertical-align:.14em;color:var(--slate)">CHF </span><span data-count="2.4" data-suffix="M">2.4M</span></b><span>Innosuisse innovation grant</span></div>
      <div class="stat reveal d2"><b>EPFL</b><span>Innovation Park, Lake Geneva</span></div>
      <div class="stat reveal d3"><b>LAES</b><span>SEALSQ-backed (NASDAQ)</span></div>
    </div>
    <div class="section-head center reveal" style="margin-top:64px;margin-bottom:30px"><p class="eyebrow" style="justify-content:center">Backed & recognised by</p></div>
    <div class="logos reveal d1">
      <span class="lg mark"><img src="<?php echo esc_url( miraex_asset_shortcode( array( "path" => "img/logos/epfl.png" ) ) ); ?>" alt="EPFL" loading="lazy"></span>
      <span class="lg mark"><img src="<?php echo esc_url( miraex_asset_shortcode( array( "path" => "img/logos/innosuisse.png" ) ) ); ?>" alt="Innosuisse" loading="lazy"></span>
      <span class="lg mark"><img src="<?php echo esc_url( miraex_asset_shortcode( array( "path" => "img/logos/innovaud.png" ) ) ); ?>" alt="Innovaud" loading="lazy"></span>
      <span class="lg mark"><img src="<?php echo esc_url( miraex_asset_shortcode( array( "path" => "img/logos/esa-bic.png" ) ) ); ?>" alt="ESA BIC" loading="lazy"></span>
      <span class="lg mark"><img src="<?php echo esc_url( miraex_asset_shortcode( array( "path" => "img/logos/ibm-q-network.png" ) ) ); ?>" alt="IBM Q Network" loading="lazy"></span>
      <span class="lg mark"><img src="<?php echo esc_url( miraex_asset_shortcode( array( "path" => "img/logos/venture-kick.png" ) ) ); ?>" alt="Venture Kick" loading="lazy"></span>
      <span class="lg mark"><img src="<?php echo esc_url( miraex_asset_shortcode( array( "path" => "img/logos/venturelab.png" ) ) ); ?>" alt="Venturelab" loading="lazy"></span>
      <span class="lg mark"><img src="<?php echo esc_url( miraex_asset_shortcode( array( "path" => "img/logos/fit.png" ) ) ); ?>" alt="FIT" loading="lazy"></span>
      <span class="lg mark"><img src="<?php echo esc_url( miraex_asset_shortcode( array( "path" => "img/logos/swiss-pic.png" ) ) ); ?>" alt="Swiss PIC" loading="lazy"></span>
      <span class="lg mark"><img src="<?php echo esc_url( miraex_asset_shortcode( array( "path" => "img/logos/swissnex.png" ) ) ); ?>" alt="Swissnex" loading="lazy"></span>
      <span class="lg mark"><img src="<?php echo esc_url( miraex_asset_shortcode( array( "path" => "img/logos/imd.svg" ) ) ); ?>" alt="IMD" loading="lazy"></span>
      <span class="lg mark"><img src="<?php echo esc_url( miraex_asset_shortcode( array( "path" => "img/logos/epic.png" ) ) ); ?>" alt="EPIC" loading="lazy"></span>
      <span class="lg mark"><img src="<?php echo esc_url( miraex_asset_shortcode( array( "path" => "img/logos/top100-swiss-startup.png" ) ) ); ?>" alt="TOP100 Swiss Startup" loading="lazy"></span>
      <span class="lg mark"><img src="<?php echo esc_url( miraex_asset_shortcode( array( "path" => "img/logos/creative-destruction-lab.png" ) ) ); ?>" alt="Creative Destruction Lab" loading="lazy"></span>
    </div>
  </div>
</section>

<!-- news rail -->
<section class="section bg-navy">
  <div class="container">
    <div class="section-head reveal" style="display:flex;justify-content:space-between;align-items:flex-end;max-width:none;gap:20px;flex-wrap:wrap">
      <div style="max-width:560px"><p class="eyebrow">Latest news</p><h2 class="h2">Signals from the lab and the group</h2></div>
      <a class="btn btn-ghost" href="<?php echo esc_url( miraex_page_url( "news" ) ); ?>">All news <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
    </div>
    <div class="grid cols-3">
      <a class="imgcard reveal" href="<?php echo esc_url( miraex_page_url( "news-sealsq-acquisition" ) ); ?>"><div class="ph"><img src="<?php echo esc_url( miraex_asset_shortcode( array( "path" => "img/orbital-qkd.jpg" ) ) ); ?>" alt="Satellite quantum link over Earth"></div><div class="bd"><div class="date">02 June 2026</div><h3>SEALSQ acquires 100% of Miraex, completing its Quantum Sovereign Vertical Stack</h3><p>The acquisition closes the quantum interconnect layer and anchors the Quantum Orbital Space Cloud.</p><span class="more">Read <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span></div></a>
      <a class="imgcard reveal d1" href="<?php echo esc_url( miraex_page_url( "news-q-modus" ) ); ?>"><div class="ph"><img src="<?php echo esc_url( miraex_asset_shortcode( array( "path" => "img/cleanroom.jpg" ) ) ); ?>" alt="Cleanroom fabrication"></div><div class="bd"><div class="date">07 April 2026</div><h3>Q-Modus SNSF Bridge: cryogenic TFLT modulator chips with EPFL</h3><p>Thin-film lithium tantalate modulators developed with Prof. Villanueva's lab and Swiss partners.</p><span class="more">Read <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span></div></a>
      <a class="imgcard reveal d2" href="<?php echo esc_url( miraex_page_url( "news-venture-leaders" ) ); ?>"><div class="ph"><img src="<?php echo esc_url( miraex_asset_shortcode( array( "path" => "img/cryostat.jpg" ) ) ); ?>" alt="Quantum hardware"></div><div class="bd"><div class="date">26 February 2025</div><h3>Miraex joins the Swiss National Startup Team in Silicon Valley</h3><p>Selected from a record 200 applicants for the Venture Leaders Technology roadshow.</p><span class="more">Read <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span></div></a>
    </div>
  </div>
</section>


<section class="section"><div class="container"><div class="cta-band reveal"><div class="inner">
  <p class="eyebrow" style="justify-content:center">Let's connect</p>
  <h2 class="h2" style="margin-bottom:16px">Interested in our TFLT PIC platform for quantum interconnectivity?</h2>
  <p class="lead" style="margin-bottom:30px">Tell us about your processor, payload or network. We'll route you to the right engineer — and, where it fits, to the wider SEALSQ quantum stack.</p>
  <div class="btn-row" style="justify-content:center">
    <a class="btn btn-primary btn-lg" href="<?php echo esc_url( miraex_page_url( "contact" ) ); ?>">Request a datasheet <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
    <a class="btn btn-ghost btn-lg" href="<?php echo esc_url( miraex_page_url( "resources" ) ); ?>">Browse resources</a>
  </div>
</div></div></div></section>
