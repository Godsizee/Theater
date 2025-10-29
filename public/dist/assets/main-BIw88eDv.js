import I from"https://cdn.jsdelivr.net/npm/colorthief@2.3.2/dist/color-thief.mjs";function T(){const t=document.querySelector("body");if(document.querySelector(".toast-container"))return;const n=document.createElement("div");n.className="toast-container",t.appendChild(n),window.showToast=(e,i="success",o=4e3)=>{const s=document.createElement("div");s.className=`toast ${i}`;const a=i==="success"?"✓":"×";s.innerHTML=`<div class="toast-icon">${a}</div><div class="toast-message"><p>${e}</p></div>`,n.appendChild(s),setTimeout(()=>s.classList.add("visible"),10),setTimeout(()=>{s.classList.remove("visible"),s.addEventListener("transitionend",()=>s.remove())},o)},window.showConfirm=(e,i)=>new Promise(o=>{const s=document.querySelector(".modal-overlay");s&&s.remove();const a=document.createElement("div");a.className="modal-overlay",a.innerHTML=`
                <div class="modal-box">
                    <h2>${e}</h2>
                    <p>${i}</p>
                    <div class="modal-actions">
                        <button class="btn btn-secondary" data-resolve="false">Abbrechen</button>
                        <button class="btn btn-danger" data-resolve="true">Ja, bestätigen</button>
                    </div>
                </div>`;const r=c=>{const l=c.target.closest("button[data-resolve]");l&&(a.classList.remove("visible"),a.addEventListener("transitionend",()=>{a.remove(),o(l.dataset.resolve==="true")}))};a.addEventListener("click",r),t.appendChild(a),setTimeout(()=>a.classList.add("visible"),10)}),window.showPasswordConfirm=e=>new Promise(i=>{const o=document.querySelector(".modal-overlay");o&&o.remove();const s=document.createElement("div");s.className="modal-overlay",s.innerHTML=`
                <div class="modal-box">
                    <h2>${e}</h2>
                    <p>Bitte geben Sie Ihr aktuelles Passwort ein, um diese Aktion zu bestätigen.</p>
                    <form id="modal-password-form">
                        <input type="password" id="modal-password-input" placeholder="Aktuelles Passwort" required style="margin-bottom: 20px;">
                        <div class="modal-actions">
                            <button type="button" class="btn btn-secondary" data-resolve="cancel">Abbrechen</button>
                            <button type="submit" class="btn btn-primary">Bestätigen</button>
                        </div>
                    </form>
                </div>`;const a=s.querySelector("#modal-password-form"),r=s.querySelector("#modal-password-input"),c=s.querySelector('[data-resolve="cancel"]'),l=d=>{s.classList.remove("visible"),s.addEventListener("transitionend",()=>{s.remove(),i(d)})};a.addEventListener("submit",d=>{d.preventDefault(),l(r.value)}),c.addEventListener("click",()=>{l(null)}),t.appendChild(s),setTimeout(()=>{s.classList.add("visible"),r.focus()},10)})}function A(){const t=window.location.pathname;document.querySelectorAll(".header-nav .header-link").forEach(e=>{const i=new URL(e.href).pathname;e.classList.remove("active"),i===t&&e.classList.add("active")})}function F(t=()=>{}){async function n(e,i=!0){const o=document.querySelector("main.page-wrapper");if(!o){console.error("SPA: .page-wrapper not found. Full page reload."),window.location.href=e;return}o.classList.add("is-exiting"),await new Promise(s=>setTimeout(s,300));try{const s=new URL(e).pathname;let a=s;s.startsWith(window.APP_CONFIG.baseUrl)&&(a=s.substring(window.APP_CONFIG.baseUrl.length)),a.startsWith("/")&&(a=a.substring(1));const r=`${window.APP_CONFIG.baseUrl}/api/get_page_content.php?path=${encodeURIComponent(a)}`,l=await(await fetch(r)).json();l.success?(o.innerHTML=l.html,document.title=l.page_title||document.title,document.body.className=l.body_class||"",i&&history.pushState({path:e},l.page_title,e),window.scrollTo(0,0),o.classList.remove("is-exiting"),o.classList.add("is-entering"),setTimeout(()=>o.classList.remove("is-entering"),300),typeof t=="function"&&t()):window.location.href=e}catch(s){console.error("SPA: Fetch error during navigation:",s),window.location.href=e}}document.body.addEventListener("click",e=>{const i=e.target.closest("a[data-spa-link]");i&&(e.preventDefault(),n(i.href))}),window.addEventListener("popstate",e=>{e.state&&e.state.path&&n(e.state.path,!1)}),(!history.state||history.state.path!==window.location.href)&&history.replaceState({path:window.location.href},document.title,window.location.href),window.navigateTo=n}function x(){const t=document.querySelector(".menu-box");t&&(t.addEventListener("mousemove",n=>{const{left:e,top:i,width:o,height:s}=t.getBoundingClientRect(),a=n.clientX-e,c=(n.clientY-i-s/2)/(s/2)*-5,l=(a-o/2)/(o/2)*5;t.style.transform=`perspective(1000px) rotateX(${c}deg) rotateY(${l}deg) scale(1.02)`}),t.addEventListener("mouseleave",()=>{t.style.transform="perspective(1000px) rotateX(0) rotateY(0) scale(1)"})),document.body.addEventListener("change",n=>{var e;if(n.target.matches(".file-upload-input")){const i=n.target,o=(e=i.closest(".file-upload-wrapper"))==null?void 0:e.querySelector(".file-upload-filename");o&&(o.textContent=i.files.length>0?i.files[0].name:"Keine Datei ausgewählt")}}),document.body.addEventListener("click",n=>{const e=n.target.closest(".collapsible-header");if(!e)return;const i=e.nextElementSibling;if(!i||!i.classList.contains("collapsible-content"))return;const o=e.getAttribute("aria-expanded")==="true";e.setAttribute("aria-expanded",String(!o)),i.classList.toggle("is-open",!o)})}function B(){const t=document.querySelector(".page-header");if(!t)return;const n=document.getElementById("mobile-menu-toggle"),e=document.getElementById("header-nav");if(n&&e){const o=()=>{n.classList.remove("is-open"),e.classList.remove("is-open"),document.body.classList.remove("menu-open"),n.setAttribute("aria-expanded","false")};n.addEventListener("click",()=>{e.classList.contains("is-open")?o():(n.classList.add("is-open"),e.classList.add("is-open"),document.body.classList.add("menu-open"),n.setAttribute("aria-expanded","true"))}),e.addEventListener("click",s=>{s.target.closest("a")&&o()})}const i=t.querySelector(".user-menu");if(i&&!i.dataset.menuInitialized){const o=i.querySelector(".user-menu-toggle"),s=i.querySelector(".user-menu-dropdown");o&&s&&(o.addEventListener("click",a=>{a.stopPropagation();const r=s.classList.toggle("is-open");o.setAttribute("aria-expanded",r)}),document.addEventListener("click",a=>{!i.contains(a.target)&&s.classList.contains("is-open")&&(s.classList.remove("is-open"),o.setAttribute("aria-expanded","false"))}),i.dataset.menuInitialized="true")}}function P(){const t=document.querySelectorAll(".movie-card, .recommendation-card");if(t.length===0)return;const n=new I;t.forEach(e=>{const i=e.querySelector("img");if(!i)return;i.crossOrigin="Anonymous";const o=(s,a)=>{try{const c=`rgb(${n.getColor(s).join(",")})`;a.style.setProperty("--accent-color",c)}catch(r){console.error("Fehler bei der Farbanalyse für Bild:",s.src,r)}};i.complete?o(i,e):(i.addEventListener("load",function(){o(this,e)}),i.addEventListener("error",function(){console.error("Bild für Farbanalyse konnte nicht geladen werden:",this.src)}))})}async function L(t,n={}){try{const e=await fetch(t,n),i=await e.json();if(!e.ok)throw new Error(i.message||`Ein Serverfehler ist aufgetreten (Status: ${e.status}).`);return i}catch(e){throw console.error("API Client Fehler:",e.message),window.showToast(e.message||"Ein unbekannter Netzwerkfehler ist aufgetreten.","error"),e}}function M(t,n){t.innerHTML="";let e="";for(let i=0;i<n;i++)e+=`
            <div class="skeleton-card">
                <div class="skeleton-poster"></div>
                <div class="skeleton-content">
                    <div class="skeleton-title"></div>
                    <div class="skeleton-meta">
                        <div class="skeleton-meta-tag"></div>
                        <div class="skeleton-meta-tag"></div>
                    </div>
                </div>
            </div>
        `;t.innerHTML=e}function z(t,n){const e=window.APP_CONFIG,i=new Intl.NumberFormat("de-DE",{style:"currency",currency:"EUR"}).format(t.price),o=n==="series"?" / Staffel":"",s=`${e.baseUrl}/${n==="movies"?"movie":"series"}/${t.slug}`,a=t.PosterPath?`${e.baseUrl}/${t.PosterPath}`:`${e.baseUrl}/img/movieImg/placeholder.png`,r=document.body.classList.contains("admin-dashboard-body");let c=`<span class="meta-tag price">${i}${o}</span>`;n==="movies"&&t.USK!==null&&(c=`<span class="meta-tag usk-${t.USK}">FSK ${t.USK}</span>`+c);let l;return r?l=`
            <a href="${e.baseUrl}/admin/update/${n==="movies"?"movie":"series"}/${t.id}" class="action-btn edit">Bearbeiten</a>
            <a href="#" class="action-btn delete" data-id="${t.id}" data-type="${n==="movies"?"movie":"series"}">Löschen</a>
        `:l=`
            <a href="#" class="action-btn add-to-cart" 
               data-id="${t.id}" 
               data-type="${n==="movies"?"movie":"series"}" 
               data-title="${t.title}" 
               data-price="${t.price}">In den Warenkorb</a>
        `,`
        <div class="movie-card">
            <a href="${s}" class="movie-card-link" data-spa-link>
                <div class="movie-card-image-container">
                    <img loading="lazy" decoding="async" src="${a}" alt="Poster von ${t.title}" class="movie-card-poster">
                </div>
                <div class="movie-card-content">
                    <h3>${t.title}</h3>
                    <div class="movie-meta">
                        ${c}
                    </div>
                </div>
            </a>
            <div class="movie-card-actions">
                ${l}
            </div>
        </div>
    `}function S(t,n,e,i){if(!t||!n||!e)return;let o=!1,s;const a=async(u,g=!1)=>{var m;if(o)return;if(o=!0,!g){const v=parseInt(u.get("items_per_page"))||12;M(t,v)}const f=n.querySelector("#load-more-btn");f&&(f.disabled=!0,f.textContent="Lade...");try{const v=`${window.APP_CONFIG.baseUrl}/api/${i}.php?${u.toString()}`,p=await L(v);if(!p.success)throw new Error(p.message);const b=p.data.items.map(w=>z(w,i)).join("");if(await new Promise(w=>setTimeout(w,300)),g?((m=t.querySelector(".skeleton-card"))==null||m.remove(),t.insertAdjacentHTML("beforeend",b)):t.innerHTML=b||'<div class="container text-center" style="grid-column: 1 / -1;"><p>Ihre Suche ergab leider keine Treffer.</p></div>',n.innerHTML="",p.data.pagination.currentPage<p.data.pagination.totalPages){const w=p.data.pagination.currentPage+1;n.innerHTML=`<button id='load-more-btn' class='btn btn-primary' data-next-page='${w}' style='width: auto;'>Weitere laden</button>`}P()}catch{t.innerHTML='<p class="message error" style="grid-column: 1 / -1;">Die Inhalte konnten nicht geladen werden.</p>'}finally{o=!1}},r=(u=!1)=>{const g=new FormData(e),f=window.APP_CONFIG.settings||{items_per_page:24},m=new URLSearchParams;for(const[p,b]of g.entries())b&&m.append(p,b);if(!u){const p=new URLSearchParams(window.location.search);!m.has("genre")&&p.has("genre")&&m.set("genre",p.get("genre"))}m.set("items_per_page",f.items_per_page),m.has("page")||m.set("page","1"),a(m,!1);const v=new URL(window.location.href);v.search=m.toString(),v.href!==window.location.href&&history.pushState({path:v.href},"",v.href)},c=()=>{const u=new URLSearchParams(window.location.search);for(const[g,f]of u.entries()){const m=e.querySelector(`[name="${g}"]`);m&&(m.value=f)}},l=u=>{const g=u.target.closest("#load-more-btn");if(g&&!o){const f=g.dataset.nextPage;if(f){const m=new URLSearchParams(new FormData(e));m.set("page",f),a(m,!0)}}},d=()=>{clearTimeout(s),s=setTimeout(()=>r(!1),400)};c(),r(!0),e.addEventListener("submit",u=>{u.preventDefault(),r(!1)});const h=e.querySelector("#search");h&&(h.addEventListener("input",d),h.addEventListener("keyup",u=>{(u.key==="Backspace"||u.key==="Delete")&&d()})),e.querySelectorAll("select").forEach(u=>{u.addEventListener("change",()=>r(!1))}),n.addEventListener("click",l)}function U(){console.log("initializeFrontendMediaLoader wurde aufgerufen!");const t=document.querySelector("#movies-grid-container"),n=document.querySelector("#pagination-container"),e=document.querySelector("#movie-filter-form");t&&n&&e&&(console.log("Frontend Film-Loader wird eingerichtet."),S(t,n,e,"movies"));const i=document.querySelector("#series-grid-container"),o=document.querySelector("#pagination-container"),s=document.querySelector("#series-filter-form");i&&o&&s&&(console.log("Frontend Serien-Loader wird eingerichtet."),S(i,o,s,"series")),!t&&!i&&console.log("initializeFrontendMediaLoader: Keine Frontend-Medien-Grids gefunden. Überspringe.")}function q(){console.log("initializeAdminMediaManager wurde aufgerufen!");const t=document.querySelector("#movies-grid-container"),n=document.querySelector("#pagination-container"),e=document.querySelector("#movie-filter-form");t&&n&&e&&(console.log("Admin Film-Manager wird eingerichtet."),S(t,n,e,"movies"));const i=document.querySelector("#series-grid-container"),o=document.querySelector("#pagination-container"),s=document.querySelector("#series-filter-form");i&&o&&s&&(console.log("Admin Serien-Manager wird eingerichtet."),S(i,o,s,"series")),!t&&!i&&console.log("initializeAdminMediaManager: Keine Admin-Medien-Grids gefunden. Überspringe.")}function N(){let t;const n=async(i,o)=>{const s=i.parentElement.querySelector(".validation-status");if(!s)return;const a=i.value.trim();if(i.classList.remove("invalid-input"),a.length<3){s.className="validation-status",s.textContent="";return}s.textContent="Prüfe...",s.className="validation-status checking visible";try{const c=await(await fetch(`${window.APP_CONFIG.baseUrl}/api/validate_user.php?${o}=${encodeURIComponent(a)}`)).json();s.textContent=c.message,s.className=`validation-status ${c.available?"valid":"invalid"} visible`,c.available||i.classList.add("invalid-input")}catch(r){s.textContent="Prüfung fehlgeschlagen.",s.className="validation-status invalid visible",console.error("Validation error:",r)}},e=i=>{const o=i.target;clearTimeout(t),t=setTimeout(()=>{o.matches("#username-input")&&n(o,"username"),o.matches("#email-input")&&n(o,"email")},500)};document.body.removeEventListener("input",e),document.body.addEventListener("input",e)}function _(){const t=async n=>{const e=n.target.closest(".action-btn.delete");if(e){if(n.preventDefault(),!await window.showConfirm("Löschen bestätigen","Sind Sie sicher, dass dieses Element endgültig gelöscht werden soll?"))return;const s=e.closest(".movie-card, .missing-data-list > li, .user-table tbody tr"),a=e.dataset.id,r=e.dataset.type;if(!a||!s||!r){console.error("Delete action aborted: Missing id, item to remove, or data-type.");return}try{const c=new FormData;c.append("id",a);const l=`${window.APP_CONFIG.baseUrl}/api/delete_${r}.php`,d=await L(l,{method:"POST",body:c});d.success?(window.showToast(d.message||"Erfolgreich gelöscht.","success"),s.classList.add("is-deleting"),s.addEventListener("transitionend",()=>s.remove())):window.showToast(d.message||"Aktion konnte nicht ausgeführt werden.","error")}catch{}}const i=n.target.closest(".action-cancel-order");if(i){n.preventDefault();const o=i.dataset.ticketId;if(!await window.showConfirm("Bestellung stornieren","Möchten Sie diese Ausleihe wirklich stornieren?"))return;const a=new FormData;a.append("ticket_id",o);try{const r=`${window.APP_CONFIG.baseUrl}/support/stornieren.php`,c=await L(r,{method:"POST",body:a});if(c.success){window.showToast(c.message,"success");const l=document.getElementById(`ticket-row-${o}`);if(l){const d=l.querySelector(".status-text");d&&(d.textContent="Storniert");const h=l.querySelector(".status-info-text");h&&(h.textContent="Soeben storniert"),i.remove()}}else window.showToast(c.message,"error")}catch{}}};document.body.removeEventListener("click",t),document.body.addEventListener("click",t)}function O(){const t=document.querySelector(".profile-content");if(!t)return;const n=()=>{t.removeEventListener("click",i),t.removeEventListener("submit",o)},e=()=>{t.addEventListener("click",i),t.addEventListener("submit",o)};function i(s){const a=s.target.closest(".edit-icon");if(a){const r=a.closest(".profile-data-section");r&&r.classList.toggle("is-editing")}}async function o(s){const a=s.target.closest(".profile-edit-form");if(!a)return;s.preventDefault();const r=await window.showPasswordConfirm("Änderungen bestätigen");if(r===null)return;if(r===""){window.showToast("Das Passwort darf nicht leer sein.","error");return}const c=new FormData(a);c.append("current_password",r);const l=`${window.APP_CONFIG.baseUrl}/profil_daten/update`;try{const d=await L(l,{method:"POST",body:c});d.success&&(window.showToast(d.message,"success"),window.navigateTo(window.location.href,!1))}catch(d){console.error("Fehler beim Aktualisieren des Profils:",d)}}n(),e()}function D(){G(),H()}function G(){const t=document.getElementById("copy-prompt-btn"),n=document.getElementById("ai-prompt");t&&n&&t.addEventListener("click",function(){this.disabled||navigator.clipboard.writeText(n.value).then(()=>{const e=this.textContent;this.textContent="Kopiert!",this.style.backgroundColor="var(--color-success)",setTimeout(()=>{this.textContent=e,this.style.backgroundColor=""},2e3)}).catch(e=>{console.error("Fehler beim Kopieren: ",e)})})}function H(){if(!document.querySelector('form[action="insert.php"]'))return;const n=document.getElementById("media-type-select"),e=document.getElementById("title-input"),i=document.getElementById("genre-input"),o=document.getElementById("ai-prompt"),s=document.getElementById("copy-prompt-btn"),a=()=>{const r=n.value==="movie"?"für den Film":"für die Serie",c=e.value.trim(),l=i.value.trim().split(",")[0];if(!c){o.value="Bitte geben Sie einen Titel und ein Genre ein...",s.disabled=!0;return}let d=`Ein dramatisches, hochwertiges Filmplakat ${r} '${c}'. `;l&&(d+=`Genre: ${l}. `),d+="Stil: filmisch, episch, hochauflösend, meisterwerk.",o.value=d,s.disabled=!1};n.addEventListener("change",a),e.addEventListener("input",a),i.addEventListener("input",a)}function R(){if(document.body.dataset.spotlightInitialized)return;document.body.dataset.spotlightInitialized="true";const t=document.createElement("div");t.className="spotlight-overlay",document.body.appendChild(t);const n=document.getElementById("search");document.body.addEventListener("focusin",e=>{e.target.matches("#search")&&document.body.classList.add("search-is-active")}),document.body.addEventListener("focusout",e=>{e.target.matches("#search")&&document.body.classList.remove("search-is-active")}),document.addEventListener("mousedown",e=>{if(!document.body.classList.contains("search-is-active"))return;const i=document.querySelector(".search-filter-container");i&&!i.contains(e.target)&&(n==null||n.blur())}),document.addEventListener("keydown",e=>{e.key==="Escape"&&document.body.classList.contains("search-is-active")&&(n==null||n.blur())})}function K(){if(document.body.dataset.filmLookInitialized)return;document.body.dataset.filmLookInitialized="true";const t=document.getElementById("film-look-toggle"),n=document.body,e=()=>{const s=n.classList.toggle("film-look-active");localStorage.setItem("filmLookActive",s)};localStorage.getItem("filmLookActive")==="true"&&n.classList.add("film-look-active"),t&&t.addEventListener("click",e);const i=document.createElement("div");i.className="film-look-vignette",n.appendChild(i);const o=document.createElement("div");o.className="film-look-grain",n.appendChild(o),console.log("Interaktiver Film-Look initialisiert.")}const E="movieRentalCart";function y(){const t=sessionStorage.getItem(E);return t?JSON.parse(t):[]}function $(t){sessionStorage.setItem(E,JSON.stringify(t))}function W(t){const n=y();return n.find(i=>i.id===t.id&&i.type===t.type)?!1:(n.push(t),$(n),k(),!0)}function j(t,n){let e=y();e=e.filter(i=>!(i.id===t&&i.type===n)),$(e),k()}function J(){sessionStorage.removeItem(E),k()}function k(){const t=y(),n=document.getElementById("cart-item-count");n&&(n.textContent=t.length,n.classList.toggle("visible",t.length>0))}k();function V(){function t(n){const e=n.target.closest(".btn:not([disabled])");if(!e)return;const i=document.createElement("span"),s=Math.max(e.clientWidth,e.clientHeight)/2,a=e.getBoundingClientRect();i.style.left=`${n.clientX-a.left-s}px`,i.style.top=`${n.clientY-a.top-s}px`,i.classList.add("ripple");const r=e.getElementsByClassName("ripple")[0];r&&r.remove(),e.appendChild(i),i.addEventListener("animationend",()=>{i.remove()})}document.removeEventListener("click",t),document.addEventListener("click",t)}function Y(){function t(n){const e=n.target.closest(".add-to-cart");if(!e)return;n.preventDefault();const i=e.closest(".movie-card, .movie-detail-container");if(!i)return;const o=parseFloat(e.dataset.price);if(isNaN(o)){console.error("Konnte keinen gültigen Preis extrahieren. Das data-price Attribut fehlt oder ist ungültig.",e),window.showToast("Fehler: Der Preis für diesen Artikel konnte nicht ermittelt werden.","error");return}const s={id:e.dataset.id,type:e.dataset.type,title:e.dataset.title,price:o,poster:i.querySelector("img").src};W(s)?window.showToast(`"${s.title}" wurde zum Warenkorb hinzugefügt.`,"success"):window.showToast(`"${s.title}" ist bereits im Warenkorb.`,"error")}document.body.removeEventListener("click",t),document.body.addEventListener("click",t)}function X(){if(typeof gsap>"u"||typeof ScrollTrigger>"u"){console.error("GSAP oder ScrollTrigger nicht geladen.");return}gsap.registerPlugin(ScrollTrigger);const t=gsap.utils.toArray(".movie-card");t.length!==0&&gsap.from(t,{opacity:0,y:50,duration:.6,stagger:.1,ease:"power3.out",scrollTrigger:{trigger:".movie-grid",start:"top 80%",end:"bottom 20%"}})}function Z(){const t=document.getElementById("checkout-container");if(!t)return;const n=window.CHECKOUT_USER_DATA||{};function e(){const i=y();if(i.length===0){t.innerHTML=`<div class="empty-cart-message"><h3>Ihr Warenkorb ist leer</h3><p>Fügen Sie Filme oder Serien hinzu, um sie hier zu sehen.</p><a href="${window.APP_CONFIG.baseUrl}/select" class="btn btn-primary" style="width: auto;" data-spa-link>Jetzt stöbern</a></div>`;return}let o=i.map(c=>`
            <div class="order-item" data-id="${c.id}" data-type="${c.type}">
                <img src="${c.poster}" alt="${c.title}" class="order-item-poster">
                <div class="order-item-details">
                    <h5>${c.title}</h5>
                    <p class="availability">Sofort lieferbar</p>
                </div>
                <div class="order-item-price">${(c.price||0).toFixed(2)} €</div>
            </div>`).join("");const s=i.reduce((c,l)=>c+(l.price||0),0),a=0,r=s+a;t.innerHTML=`
            <div class="checkout-layout">
                <div class="customer-info-col">
                    <div class="info-box">
                        <div class="info-box-header">
                            <h4>Rechnungsanschrift</h4>
                            <a href="${window.APP_CONFIG.baseUrl}/profil_daten" class="edit-link" data-spa-link>Bearbeiten</a>
                        </div>
                        <address>
                            ${n.Vorname||""} ${n.Nachname||""}<br>
                            ${n.Strasse||""} ${n.Hausnummer||""}<br>
                            ${n.PLZ||""} ${n.Ort||""}
                        </address>
                    </div>
                    <div class="info-box">
                        <div class="info-box-header"><h4>Zahlungsarten</h4></div>
                        <div class="payment-options">
                            <div class="payment-option selected" data-payment="paypal">
                                <img src="${window.APP_CONFIG.baseUrl}/public/assets/icons/paypalblack.png" alt="PayPal" class="payment-icon">
                                <span class="payment-label">PayPal</span>
                            </div>
                            <div class="payment-option" data-payment="klarna">
                                <img src="${window.APP_CONFIG.baseUrl}/public/assets/icons/klarnablack.png" alt="Klarna" class="payment-icon">
                                <span class="payment-label">Klarna</span>
                            </div>
                            <div class="payment-option" data-payment="visa">
                                <img src="${window.APP_CONFIG.baseUrl}/public/assets/icons/visablack.png" alt="Visa" class="payment-icon">
                                <span class="payment-label">Kreditkarte</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="order-details-col">
                    <h4>Ihre Bestellung</h4>
                    <div class="order-items-container">${o}</div>
                    <div class="order-summary">
                        <div class="summary-line"><span>Versandkosten</span><span>${a.toFixed(2)} €</span></div>
                        <div class="summary-line total"><span>Gesamtsumme (inkl. MwSt.)</span><span>${r.toFixed(2)} €</span></div>
                    </div>
                    <div class="final-confirmation">
                        <div class="terms-agreement">
                           <label>
                               <input type="checkbox" id="terms-checkbox">
                               <span>Ich habe die <a href="${window.APP_CONFIG.baseUrl}/agb" target="_blank" data-spa-link>AGB</a> gelesen und stimme ihnen zu.</span>
                           </label>
                        </div>
                        <button id="submit-order-btn" class="btn btn-success">Kaufen</button>
                    </div>
                </div>
            </div>`}t.addEventListener("click",async i=>{if(i.target.matches("#submit-order-btn")){const s=document.getElementById("terms-checkbox");if(!s||!s.checked){window.showToast("Bitte stimmen Sie den AGB zu, um fortzufahren.","error");return}const a=i.target;a.disabled=!0,a.textContent="Bestellung wird verarbeitet...";try{const r=`${window.APP_CONFIG.baseUrl}/api/create_order.php`,c={method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify(y())},l=await L(r,c);l.success&&(J(),window.showToast(l.message,"success"),setTimeout(()=>window.navigateTo(`${window.APP_CONFIG.baseUrl}/bestellungen`),1500))}catch{a.disabled=!1,a.textContent="Kaufen"}}const o=i.target.closest(".payment-option");o&&(t.querySelectorAll(".payment-option").forEach(s=>s.classList.remove("selected")),o.classList.add("selected"))}),e()}function Q(){const t=document.getElementById("cart-container");if(!t)return;function n(){const e=y();if(e.length===0){t.innerHTML=`
                <div class="empty-cart-message">
                    <h3>Ihr Warenkorb ist leer</h3>
                    <p>Fügen Sie Filme oder Serien hinzu, um sie hier zu sehen.</p>
                    <a href="${window.APP_CONFIG.baseUrl}/select" class="btn btn-primary" style="width: auto;" data-spa-link>Jetzt stöbern</a>
                </div>`;return}let i=e.map(s=>`
            <div class="cart-item" data-id="${s.id}" data-type="${s.type}">
                <img src="${s.poster}" alt="${s.title}" class="cart-item-poster">
                <div class="cart-item-details">
                    <h4>${s.title}</h4>
                    <div class="cart-item-price">${(s.price||0).toFixed(2)} €</div>
                </div>
                <div class="cart-item-actions">
                    <button class="remove-from-cart-btn" title="Entfernen">&times;</button>
                </div>
            </div>
        `).join("");const o=e.reduce((s,a)=>s+(a.price||0),0);t.innerHTML=`
            <div class="cart-layout">
                <div class="cart-items-list-container">
                    <h3>Ihre Auswahl</h3>
                    <div class="cart-items-list">
                        ${i}
                    </div>
                </div>
                <div class="order-summary-card">
                    <h3>Zusammenfassung</h3>
                    <div class="summary-line">
                        <span>Zwischensumme:</span>
                        <span>${o.toFixed(2)} €</span>
                    </div>
                    <div class="summary-line">
                        <small>Versandkosten werden an der Kasse berechnet.</small>
                    </div>
                    <a href="${window.APP_CONFIG.baseUrl}/checkout" class="btn btn-success" data-spa-link style="width: 100%; margin-top: 20px;">Zur Kasse</a>
                </div>
            </div>`}t.addEventListener("click",e=>{if(e.target.closest(".remove-from-cart-btn")){const i=e.target.closest(".cart-item");j(i.dataset.id,i.dataset.type),n()}}),n()}function ee(){const t=document.getElementById("theme-toggle"),n=document.documentElement,e=o=>{o==="dark"?n.classList.add("dark-mode"):n.classList.remove("dark-mode")},i=()=>{const o=n.classList.contains("dark-mode")?"light":"dark";localStorage.setItem("theme",o),e(o)};t&&t.addEventListener("click",i)}function C(){console.log("Führe Inhalts-Initialisierer aus..."),[x,N,_,O,D,A,P,X].forEach(n=>{if(typeof n=="function")try{n()}catch(e){console.error(`Fehler bei der Ausführung von ${n.name}:`,e)}}),document.getElementById("movies-grid-container")||document.getElementById("series-grid-container")?U():document.querySelector(".admin-dashboard-body #movies-grid-container")||document.querySelector(".admin-dashboard-body #series-grid-container")?q():document.getElementById("checkout-container")?Z():document.getElementById("cart-container")&&Q()}function te(){console.log("Führe globale Initialisierer aus..."),ee(),T(),R(),K(),B(),V(),Y(),k(),F(C)}document.addEventListener("DOMContentLoaded",()=>{te(),C()});
