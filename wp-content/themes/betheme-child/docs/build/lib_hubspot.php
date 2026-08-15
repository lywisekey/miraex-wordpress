<?php
/**
 * The /contact/ form, posting straight to HubSpot's Forms Submission API.
 *
 * Why not the js.hsforms.net embed: it renders into a cross-origin iframe, so none of
 * the design (field radius, cyan focus ring, the pill button) can be styled from this
 * site. The submission endpoint is public, so nothing secret ends up in the page.
 *
 * The field names were confirmed against the live form rather than guessed: an
 * incomplete POST comes back with REQUIRED_FIELD errors naming every required field,
 * and no contact is created while checking. HubSpot **ignores unknown field names
 * silently** — a wrong name loses the answer without any error, which is why
 * `comments` (not `message`) and `miraex_intent` matter.
 */

const HS_PORTAL = '9201667';
const HS_FORM   = 'f1490e22-9dbb-479d-8c82-79d87fb33f1a';
const HS_REGION = 'na1';

/** Checkbox property, not a HubSpot legal-consent object — it takes a plain "true". */
const HS_CONSENT_VALUE = 'true';

/**
 * label shown in the select => value stored in the CRM.
 *
 * Confirmed against the `miraex_intent` property: its CRM values are the labels
 * verbatim, so the two sides are identical here. Keep them in step if HubSpot changes.
 */
function hs_intent_options() {
	return [
		'OEM design / evaluation (under NDA)' => 'OEM design / evaluation (under NDA)',
		'Exclusive licensing'                 => 'Exclusive licensing',
		'Distributed quantum computing'       => 'Distributed quantum computing',
		'Quantum sensing'                     => 'Quantum sensing',
		'Quantum networking / QKD'            => 'Quantum networking / QKD',
		'Partnership'                         => 'Partnership',
		'Press / analyst'                     => 'Press / analyst',
		'Careers'                             => 'Careers',
	];
}

/** The form as a BeBuilder plain_text item, styled exactly like the CF7 one it replaces. */
function hs_form_item( $key ) {
	global $C, $F_TEXT, $F_DISPLAY;

	$INTENT = hs_intent_options();

$options = '<option value="" disabled selected>Select an intent…</option>';
	
	foreach ( $INTENT as $label => $value ) {
		$options .= '<option value="' . esc_attr( $value ) . '">' . esc_html( $label ) . '</option>';
	}
	
	$req = '<span style="color:#19c6da">*</span>';
	
	$form = '
	<form class="miraex-hs-form" novalidate>
		<div style="display:grid;grid-template-columns:1fr 1fr;gap:18px">
			<label>First name ' . $req . '<input type="text" name="firstname" autocomplete="given-name" placeholder="Jane" required></label>
			<label>Last name ' . $req . '<input type="text" name="lastname" autocomplete="family-name" placeholder="Doe" required></label>
		</div>
		<label>Work email ' . $req . '<input type="email" name="email" autocomplete="email" placeholder="jane@organisation.com" required></label>
		<label>Organisation<input type="text" name="company" autocomplete="organization" placeholder="Company / institution"></label>
		<label>How can we help? ' . $req . '<select name="miraex_intent" required>' . $options . '</select></label>
		<label>Message ' . $req . '<textarea name="comments" rows="6" placeholder="A few lines about your application or architecture…" required></textarea></label>
		<label class="miraex-hs-consent"><input type="checkbox" name="consent" required> I agree to the privacy policy and terms of service.</label>
	
		<!-- Bot trap. The name and label are deliberately meaningless: called
		     "website_url" with a "Company website" label, Chrome autofill and password
		     managers recognised it and filled it in for real visitors, whose messages were
		     then dropped as bot traffic. Nothing here should look like a real field. -->
		<div class="miraex-hs-hp" aria-hidden="true"><label>Leave this field empty<input type="text" name="mx_r9" tabindex="-1" autocomplete="off"></label></div>
	
		<div class="miraex-hs-turnstile"></div>
		<button type="submit">Send message</button>
		<p class="miraex-hs-status" role="alert" hidden></p>
		<p class="miraex-hs-note">No puzzles, no friction — protected by invisible bot detection.</p>
	</form>

	<div class="miraex-hs-done" role="status" aria-live="polite" tabindex="-1" hidden>
		<svg class="miraex-hs-tick" viewBox="0 0 44 44" aria-hidden="true" focusable="false">
			<circle cx="22" cy="22" r="20" fill="none" stroke="currentColor" stroke-width="1.5" opacity="0.4"></circle>
			<path d="M13 22.5l6 6 12-13" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"></path>
		</svg>
		<h3>Message sent</h3>
		<p>Thank you — your enquiry is with us. We’ll route it to the right engineer, and you’ll hear back from a person, usually within two working days.</p>
		<button type="button" class="miraex-hs-again">Send another message</button>
	</div>
	
	<script>
	(function () {
		var form = document.currentScript.previousElementSibling;
		while ( form && form.tagName !== "FORM" ) { form = form.previousElementSibling; }
		if ( ! form ) { return; }
	
		var status = form.querySelector( ".miraex-hs-status" );
		var button = form.querySelector( "button[type=submit]" );
		var done   = form.parentElement.querySelector( ".miraex-hs-done" );
		var again  = done.querySelector( ".miraex-hs-again" );
		var widget = null;

		/* Rendered only when a site key is configured; without one the form works exactly
		   as it does now and the server skips verification. */
		if ( config.turnstile ) {
			( function render() {
				if ( ! window.turnstile ) { setTimeout( render, 200 ); return; }
				widget = window.turnstile.render( form.querySelector( ".miraex-hs-turnstile" ), {
					sitekey: config.turnstile,
					theme: "dark"
				} );
			} )();
		}

		/* Set by a real key press or pointer press inside the form. A script posting the
		   form never produces either, so this separates autofill from automation. */
		var touched = false;

		[ "keydown", "pointerdown" ].forEach( function ( type ) {
			form.addEventListener( type, function () { touched = true; }, { passive: true } );
		} );
		/* The browser no longer talks to HubSpot: it posts here, and inc/hubspot-proxy.php
		   validates, rate-limits and forwards. The portal id and form GUID stay on the
		   server, so nobody can lift them from this page and post to HubSpot directly. */
		var config   = window.MIRAEX_CONTACT || {};
		var endpoint = config.endpoint || "/wp-json/miraex/v1/contact";
	
		function cookie( name ) {
			var m = document.cookie.match( "(^|;)\\\\s*" + name + "\\\\s*=\\\\s*([^;]+)" );
			return m ? m.pop() : "";
		}
	
		/* An error stays inline beside the button; success replaces the form outright.
		A line of text under a form that still looks filled in does not read as "sent" —
		which is exactly how it got missed. */
		function fail( text ) {
			status.textContent = text;
			status.hidden = false;
			button.disabled = false;
			button.textContent = "Send message";
		}

		function succeed() {
			form.reset();
			form.hidden = true;
			done.hidden = false;

			var still = window.matchMedia( "(prefers-reduced-motion: reduce)" ).matches;
			done.scrollIntoView( { behavior: still ? "auto" : "smooth", block: "center" } );
			done.focus( { preventScroll: true } );
		}

		again.addEventListener( "click", function () {
			done.hidden = true;
			form.hidden = false;
			status.hidden = true;
			button.disabled = false;
			button.textContent = "Send message";
			if ( widget && window.turnstile ) { window.turnstile.reset( widget ); }
			form.elements.firstname.focus();
		} );
	
		form.addEventListener( "submit", function ( event ) {
			event.preventDefault();
	
			if ( ! form.checkValidity() ) { form.reportValidity(); return; }
	
			var payload = {
				hutk: cookie( "hubspotutk" ),
				pageUri: window.location.href,
				pageName: document.title,
				/* the trap travels with the submission; the server decides what it means */
				mx_r9: touched ? "" : form.elements.mx_r9.value
			};

			[ "firstname", "lastname", "email", "company", "miraex_intent", "comments" ].forEach( function ( name ) {
				var el = form.elements[ name ];
				payload[ name ] = el ? el.value : "";
			} );

			if ( config.turnstile && window.turnstile ) {
				payload.turnstile = window.turnstile.getResponse( widget );
			}
	
			button.disabled = true;
			status.hidden = true;
			button.textContent = "Sending…";
	
			fetch( endpoint, {
				method: "POST",
				headers: { "Content-Type": "application/json" },
				body: JSON.stringify( payload )
			} ).then( function ( response ) {
				return response.json().then( function ( body ) { return { ok: response.ok, body: body }; } );
			} ).then( function ( result ) {
				if ( result.ok && result.body && result.body.ok ) { succeed(); return; }

				fail( ( result.body && result.body.message ) || "That did not go through. Please email info@miraex.com and we will pick it up there." );
			} ).catch( function () {
				fail( "That did not go through — check your connection, or email info@miraex.com and we will pick it up there." );
			} );
		} );
	})();
	</script>';

	$FIELD = ITEM . ' form input:not([type=checkbox]):not([type=submit]),' . ITEM . ' form select,' . ITEM . ' form textarea';
	$BTN   = ITEM . ' form button';

	/* Chrome paints an autofilled field with its own near-white background and will not
	   let `background-color` override it — on a dark form that leaves white text on a
	   white ground. The inset box-shadow is the only way to repaint it, and
	   -webkit-text-fill-color the only way to keep the text readable. */
	$AUTOFILL = implode( ',', [
		ITEM . ' form input:-webkit-autofill',
		ITEM . ' form input:-webkit-autofill:hover',
		ITEM . ' form input:-webkit-autofill:focus',
		ITEM . ' form select:-webkit-autofill',
		ITEM . ' form select:-webkit-autofill:hover',
		ITEM . ' form select:-webkit-autofill:focus',
		ITEM . ' form textarea:-webkit-autofill',
	] );

	return item( $key, 'plain_text', 'Contact form — HubSpot', '1/1', array_merge( col_margin( '10px' ), [
		'content' => $form,

		/* labels */
		'css_form_color'      => css( ITEM . ' form,' . ITEM . ' form label', 'color', $C['inkSoft'] ),
		'css_form_typography' => css( ITEM . ' form,' . ITEM . ' form label', 'typography', [
			'desktop'     => [ 'font-size' => '14px', 'font-family' => $F_TEXT ],
			'font-weight' => '500',
		] ),
		'css_form_label_block' => css( ITEM . ' form label', 'display', 'block' ),
		'css_form_gap'         => css( ITEM . ' form label', 'margin', [ 'desktop' => dim( '0px', '0px', '18px', '0px' ) ] ),

		/* fields — same geometry the CF7 element had */
		'css_field_width'          => css( $FIELD, 'width', '100%' ),
		'css_field_display'        => css( $FIELD, 'display', 'block' ),
		'css_field_margin'         => css( $FIELD, 'margin', [ 'desktop' => dim( '7px', '0px', '0px', '0px' ) ] ),
		/* BeTheme ships `.dark input, .dark select, .dark textarea { background:#fff;
		   color:#626262 }` in an inline <style>. These win on specificity today, but the
		   !important removes the question in every state. */
		'css_field_bg'             => css( $FIELD, 'background-color', 'rgba(255,255,255,0.03) !important' ),
		'css_field_color'          => css( $FIELD, 'color', $C['white'] . ' !important' ),
		/* Tells Chrome the controls live on a dark ground. Its autofill background comes
		   from -internal-light-dark(rgb(232,240,254), rgba(70,90,126,0.4)) with a UA
		   !important that no author rule can beat — but declaring the scheme makes it
		   resolve to the dark branch, with white text. That is the fix; the inset
		   box-shadow below is only a fallback for browsers that ignore color-scheme. */
		'css_field_scheme'         => css( $FIELD, 'color-scheme', 'dark' ),
		'css_form_scheme'          => css( ITEM . ' form', 'color-scheme', 'dark' ),
		'css_field_border_color'   => css( $FIELD, 'border-color', $C['line'] ),
		'css_field_border_style'   => css( $FIELD, 'border-style', 'solid' ),
		'css_field_border_width'   => css( $FIELD, 'border-width', [ 'desktop' => '1px 1px 1px 1px' ] ),
		'css_field_border_radius'  => css( $FIELD, 'border-radius', [ 'desktop' => '10px 10px 10px 10px' ] ),
		'css_field_padding'        => css( $FIELD, 'padding', [ 'desktop' => dim( '13px', '14px', '13px', '14px' ) ] ),
		'css_field_typography'     => css( $FIELD, 'typography', [
			'desktop' => [ 'font-size' => '15px', 'font-family' => $F_TEXT ],
		] ),
		'css_field_placeholder'    => css( ITEM . ' form input::placeholder,' . ITEM . ' form textarea::placeholder', 'color', $C['slate'] ),
		'css_field_focus'          => css( ITEM . ' form input:focus,' . ITEM . ' form select:focus,' . ITEM . ' form textarea:focus', 'border-color', $C['cyan'] ),
		'css_field_outline'        => css( ITEM . ' form input:focus,' . ITEM . ' form select:focus,' . ITEM . ' form textarea:focus', 'outline', 'none' ),

		/* consent row and the invisible bot trap */
		/* .check{display:flex;gap:11px;align-items:flex-start;font-size:13.5px}
		   .check input{width:18px;height:18px;margin-top:2px;accent-color:var(--cyan)}
		   The 2px only lands on the text's optical centre while the row keeps the
		   reference's 1.65 line-height — BeTheme's 28px line box pushes the glyphs down
		   4px and the box no longer lines up with them. */
		'css_consent_flex'   => css( ITEM . ' form .miraex-hs-consent', 'display', 'flex' ),
		'css_consent_gap'    => css( ITEM . ' form .miraex-hs-consent', 'column-gap', '11px' ),
		'css_consent_align'  => css( ITEM . ' form .miraex-hs-consent', 'align-items', 'flex-start' ),
		'css_consent_type'   => css( ITEM . ' form .miraex-hs-consent', 'typography', [
			'desktop' => [ 'font-size' => '13.5px', 'line-height' => '1.65', 'font-family' => $F_TEXT ],
		] ),
		'css_consent_color'  => css( ITEM . ' form .miraex-hs-consent', 'color', $C['slate'] ),
		'css_consent_w'      => css( ITEM . ' form .miraex-hs-consent input', 'width', '18px' ),
		'css_consent_h'      => css( ITEM . ' form .miraex-hs-consent input', 'height', '18px' ),
		'css_consent_flex0'  => css( ITEM . ' form .miraex-hs-consent input', 'flex-shrink', '0.0001' ),
		'css_consent_accent' => css( ITEM . ' form .miraex-hs-consent input', 'accent-color', $C['cyan'] ),
		'css_consent_size'   => css( ITEM . ' form .miraex-hs-consent input', 'margin', [ 'desktop' => dim( '2px', '0px', '0px', '0px' ) ] ),
		'css_hp_hidden'      => css( ITEM . ' form .miraex-hs-hp', 'display', 'none' ),

		/* submit — matches .btn-primary */
		'css_btn_bg'         => css( $BTN, 'background-color', $C['cyan'] ),
		'css_btn_color'      => css( $BTN, 'color', $C['dark'] ),
		'css_btn_border'     => css( $BTN, 'border-style', 'none' ),
		'css_btn_radius'     => css( $BTN, 'border-radius', [ 'desktop' => '999px 999px 999px 999px' ] ),
		'css_btn_padding'    => css( $BTN, 'padding', [ 'desktop' => dim( '16px', '30px', '16px', '30px' ) ] ),
		'css_btn_margin'     => css( $BTN, 'margin', [ 'desktop' => dim( '4px', '0px', '0px', '0px' ) ] ),
		'css_btn_cursor'     => css( $BTN, 'cursor', 'pointer' ),
		'css_btn_hover'      => css( $BTN . ':hover', 'background-color', $C['cyanBr'] ),
		'css_btn_disabled'   => css( $BTN . '[disabled]', 'opacity', '0.55' ),
		'css_btn_typography' => css( $BTN, 'typography', [
			'desktop'        => [ 'font-size' => '15px', 'font-family' => $F_TEXT ],
			'font-weight'    => '600',
			'letter-spacing' => '0.01em',
		] ),

		/* The select must give up its native widget rendering first. While Chrome draws
		   it as an OS control, the autofill background is painted by the control itself
		   and an inset shadow lands underneath it — the field stays white with white
		   text on it. With appearance:none it is an ordinary box again and the shadow
		   covers the autofill colour, so the chevron has to be drawn here too. */
		'css_select_appearance'  => css( ITEM . ' form select', 'appearance', 'none' ),
		'css_select_appearance2' => css( ITEM . ' form select', '-webkit-appearance', 'none' ),
		'css_select_chevron'     => css( ITEM . ' form select', 'background-image',
			"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23aebdce' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E" ),
		'css_select_chev_repeat' => css( ITEM . ' form select', 'background-repeat', 'no-repeat' ),
		'css_select_chev_pos'    => css( ITEM . ' form select', 'background-position', 'right 14px center' ),
		'css_select_chev_size'   => css( ITEM . ' form select', 'background-size', '16px 16px' ),
		'css_select_padding'     => css( ITEM . ' form select', 'padding', [ 'desktop' => dim( '13px', '40px', '13px', '14px' ) ] ),

		'css_autofill_shadow' => css( $AUTOFILL, '-webkit-box-shadow', '0 0 0 1000px rgba(17,34,52,1) inset !important' ),
		'css_autofill_text'   => css( $AUTOFILL, '-webkit-text-fill-color', $C['white'] . ' !important' ),
		'css_autofill_caret'  => css( $AUTOFILL, 'caret-color', $C['white'] ),
		/* Chrome repaints the background after a delay; pushing the transition out keeps
		   the override in place instead of letting it flash back to near-white. */
		'css_autofill_trans'  => css( $AUTOFILL, 'transition', 'background-color 600000s 0s' ),

		/* the open dropdown list is drawn by the OS — give it the same dark ground so it
		   is not white-on-white either */
		'css_autofill_sel_shadow' => css( ITEM . ' form select:-internal-autofill-selected', '-webkit-box-shadow', '0 0 0 1000px rgba(17,34,52,1) inset !important' ),
		'css_autofill_sel_text'   => css( ITEM . ' form select:-internal-autofill-selected', '-webkit-text-fill-color', $C['white'] . ' !important' ),
		'css_autofill_sel_bg'     => css( ITEM . ' form select:-internal-autofill-selected', 'background-color', 'rgba(255,255,255,0.03) !important' ),

		'css_option_bg'    => css( ITEM . ' form select option', 'background-color', $C['navy900'] ),
		'css_option_color' => css( ITEM . ' form select option', 'color', $C['white'] ),

		/* error alert — a bordered block, not a stray line of text */
		'css_status_typography' => css( ITEM . ' form .miraex-hs-status', 'typography', [
			'desktop' => [ 'font-size' => '14px', 'line-height' => '1.55', 'font-family' => $F_TEXT ],
		] ),
		'css_status_margin'  => css( ITEM . ' form .miraex-hs-status', 'margin', [ 'desktop' => dim( '16px', '0px', '0px', '0px' ) ] ),
		'css_status_pad'     => css( ITEM . ' form .miraex-hs-status', 'padding', [ 'desktop' => dim( '12px', '14px', '12px', '14px' ) ] ),
		'css_status_color'   => css( ITEM . ' form .miraex-hs-status', 'color', '#ffb4b4' ),
		'css_status_bg'      => css( ITEM . ' form .miraex-hs-status', 'background-color', 'rgba(255,120,120,0.07)' ),
		'css_status_bstyle'  => css( ITEM . ' form .miraex-hs-status', 'border-style', 'solid' ),
		'css_status_bcolor'  => css( ITEM . ' form .miraex-hs-status', 'border-color', 'rgba(255,120,120,0.32)' ),
		'css_status_bwidth'  => css( ITEM . ' form .miraex-hs-status', 'border-width', [ 'desktop' => '1px 1px 1px 1px' ] ),
		'css_status_radius'  => css( ITEM . ' form .miraex-hs-status', 'border-radius', [ 'desktop' => '10px 10px 10px 10px' ] ),

		'css_turnstile_margin' => css( ITEM . ' form .miraex-hs-turnstile:not(:empty)', 'margin', [ 'desktop' => dim( '4px', '0px', '18px', '0px' ) ] ),

		/* the note that used to sit outside the form, so it disappears with it */
		'css_note_type'   => css( ITEM . ' form .miraex-hs-note', 'typography', [
			'desktop' => [ 'font-size' => '13px', 'line-height' => '1.5', 'font-family' => $F_TEXT ],
		] ),
		'css_note_color'  => css( ITEM . ' form .miraex-hs-note', 'color', $C['slate'] ),
		'css_note_margin' => css( ITEM . ' form .miraex-hs-note', 'margin', [ 'desktop' => dim( '14px', '0px', '0px', '0px' ) ] ),

		/* confirmation panel — a .card, so it reads as a piece of the page and not as
		   an alert that flashed up next to a form still full of the visitor's answers */
		'css_done_pad'     => css( ITEM . ' .miraex-hs-done', 'padding', [ 'desktop' => dim( '34px', '30px', '34px', '30px' ) ] ),
		'css_done_bg'      => css( ITEM . ' .miraex-hs-done', 'background-color', 'rgba(25,198,218,0.05)' ),
		'css_done_bstyle'  => css( ITEM . ' .miraex-hs-done', 'border-style', 'solid' ),
		'css_done_bcolor'  => css( ITEM . ' .miraex-hs-done', 'border-color', 'rgba(25,198,218,0.30)' ),
		'css_done_bwidth'  => css( ITEM . ' .miraex-hs-done', 'border-width', [ 'desktop' => '1px 1px 1px 1px' ] ),
		'css_done_radius'  => css( ITEM . ' .miraex-hs-done', 'border-radius', [ 'desktop' => '14px 14px 14px 14px' ] ),
		'css_done_outline' => css( ITEM . ' .miraex-hs-done:focus', 'outline', 'none' ),

		'css_tick_color'  => css( ITEM . ' .miraex-hs-tick', 'color', $C['cyan'] ),
		'css_tick_w'      => css( ITEM . ' .miraex-hs-tick', 'width', '44px' ),
		'css_tick_h'      => css( ITEM . ' .miraex-hs-tick', 'height', '44px' ),
		'css_tick_margin' => css( ITEM . ' .miraex-hs-tick', 'margin', [ 'desktop' => dim( '0px', '0px', '18px', '0px' ) ] ),

		'css_done_h3'      => css( ITEM . ' .miraex-hs-done h3', 'typography', [
			'desktop'        => [ 'font-size' => '24px', 'line-height' => '1.2', 'font-family' => $F_DISPLAY ],
			'font-weight'    => '600',
			'letter-spacing' => '-0.02em',
		] ),
		'css_done_h3_color'  => css( ITEM . ' .miraex-hs-done h3', 'color', $C['white'] ),
		'css_done_h3_margin' => css( ITEM . ' .miraex-hs-done h3', 'margin', [ 'desktop' => dim( '0px', '0px', '10px', '0px' ) ] ),

		'css_done_p'        => css( ITEM . ' .miraex-hs-done p', 'typography', [
			'desktop' => [ 'font-size' => '15.5px', 'line-height' => '1.65', 'font-family' => $F_TEXT ],
		] ),
		'css_done_p_color'  => css( ITEM . ' .miraex-hs-done p', 'color', $C['inkSoft'] ),
		'css_done_p_margin' => css( ITEM . ' .miraex-hs-done p', 'margin', [ 'desktop' => dim( '0px', '0px', '22px', '0px' ) ] ),

		/* .btn-ghost */
		'css_again_bg'      => css( ITEM . ' .miraex-hs-again', 'background-color', 'rgba(255,255,255,0.02)' ),
		'css_again_color'   => css( ITEM . ' .miraex-hs-again', 'color', $C['white'] ),
		'css_again_bstyle'  => css( ITEM . ' .miraex-hs-again', 'border-style', 'solid' ),
		'css_again_bcolor'  => css( ITEM . ' .miraex-hs-again', 'border-color', $C['line'] ),
		'css_again_bwidth'  => css( ITEM . ' .miraex-hs-again', 'border-width', [ 'desktop' => '1px 1px 1px 1px' ] ),
		'css_again_radius'  => css( ITEM . ' .miraex-hs-again', 'border-radius', [ 'desktop' => '999px 999px 999px 999px' ] ),
		'css_again_pad'     => css( ITEM . ' .miraex-hs-again', 'padding', [ 'desktop' => dim( '13px', '26px', '13px', '26px' ) ] ),
		'css_again_cursor'  => css( ITEM . ' .miraex-hs-again', 'cursor', 'pointer' ),
		'css_again_hover'   => css( ITEM . ' .miraex-hs-again:hover', 'border-color', $C['cyan'] ),
		'css_again_hoverbg' => css( ITEM . ' .miraex-hs-again:hover', 'background-color', 'rgba(25,198,218,0.08)' ),
		'css_again_type'    => css( ITEM . ' .miraex-hs-again', 'typography', [
			'desktop'     => [ 'font-size' => '15px', 'line-height' => '1.65', 'font-family' => $F_TEXT ],
			'font-weight' => '600',
		] ),
	] ) );
}
