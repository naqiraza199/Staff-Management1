<style>
:root {
    --primary:#3b82f6;
    --accent:#93c5fd;
}
.tp-hidden{display:none}
.tp-box{
    position:absolute; background:#fff; border:1px solid #e5e7eb;
    border-radius:8px; padding:10px; width:200px;
    box-shadow:0 4px 12px rgba(0,0,0,.1); z-index:9999;
}
.tp-row{display:flex; gap:8px}
.tp-col{flex:1}
#tp-hours{max-height:120px; overflow-y:auto}
.tp-slot{
    padding:4px; text-align:center; border-radius:4px;
    cursor:pointer; font-weight:500; font-size:12px;
}
.tp-slot.active{background:var(--primary); color:#fff}
.tp-slot:hover{background:#ede9fe}
.tp-btn{
    width:100%; padding:6px; border-radius:4px;
    background:#e5e7eb; border:none; cursor:pointer; font-size:11px;
}
.tp-btn.active{background:var(--primary); color:#fff}
#tp-hours {
    max-height: 120px;
    overflow-y: auto;
    padding-right: 6px;
    scroll-behavior: smooth;
}

/* Chrome, Edge, Safari */
#tp-hours::-webkit-scrollbar {
    width: 6px;
}

#tp-hours::-webkit-scrollbar-track {
    background: #eef2ff;
    border-radius: 6px;
}

#tp-hours::-webkit-scrollbar-thumb {
    background: #3b82f6;
    border-radius: 6px;
}

#tp-hours::-webkit-scrollbar-thumb:hover {
    background: #2563eb;
}

/* Firefox */
#tp-hours {
    scrollbar-width: thin;
    scrollbar-color: #3b82f6 #eef2ff;
}

</style>

<div id="tp-box" class="tp-box tp-hidden">
    <div class="tp-row">
        <div class="tp-col" id="tp-hours"></div>
        <div class="tp-col" id="tp-mins"></div>
        <div class="tp-col">
            <button id="tp-am" class="tp-btn">AM</button>
            <button id="tp-pm" class="tp-btn" style="margin-top:6px">PM</button>
        </div>
    </div>
</div>


<script>
(function(){
    // Elements for the floating picker box (keep your existing HTML/CSS)
    const box = document.getElementById('tp-box');
    const hoursEl = document.getElementById('tp-hours');
    const minsEl = document.getElementById('tp-mins');
    const amBtn = document.getElementById('tp-am');
    const pmBtn = document.getElementById('tp-pm');

    // state
    let activeInput = null;   // the real input element Filament binds to
    let displaySpan = null;   // visual span showing 12-hour time
    let h = 12, m = 0, p = 'AM';
    const mins = [0,15,30,45];

    // build hour slots (1..12)
    hoursEl.innerHTML = '';
    for(let i=1;i<=12;i++){
        const d=document.createElement('div');
        d.textContent=String(i).padStart(2,'0');
        d.className='tp-slot';
        d.dataset.hour = i;
        d.addEventListener('click', () => { h = i; update(); applySelection(); });
        hoursEl.appendChild(d);
    }

    // build minute slots
    minsEl.innerHTML = '';
    mins.forEach(v=>{
        const d=document.createElement('div');
        d.textContent=String(v).padStart(2,'0');
        d.className='tp-slot';
        d.dataset.min = v;
        d.addEventListener('click', () => { m = v; update(); applySelection(); });
        minsEl.appendChild(d);
    });

    amBtn.addEventListener('click', ()=>{ p='AM'; update(); applySelection(); });
    pmBtn.addEventListener('click', ()=>{ p='PM'; update(); applySelection(); });

    function update(){
        // highlight selections
        [...hoursEl.children].forEach(e => e.classList.toggle('active', +e.textContent === h));
        [...minsEl.children].forEach(e => e.classList.toggle('active', +e.textContent === m));
        amBtn.classList.toggle('active', p==='AM');
        pmBtn.classList.toggle('active', p==='PM');

        // update visible display span
        if(displaySpan){
            displaySpan.textContent = String(h).padStart(2,'0') + ':' + String(m).padStart(2,'0') + ' ' + p;
        }
    }

    // convert current h,m,p to 24-hour number
    function to24(){
        if(p === 'AM') return h === 12 ? 0 : h;
        return h === 12 ? 12 : h + 12;
    }

    // apply selection: sets the real input.value (24-hour) and dispatches input/change
    function applySelection(){
        if(!activeInput) return;

        const hh24 = to24();
        const value24 = String(hh24).padStart(2,'0') + ':' + String(m).padStart(2,'0');

        // keep the real input value as 24-hour for Filament/Livewire
        activeInput.value = value24;

        // dispatch events so Livewire/Filament sees the change
        activeInput.dispatchEvent(new Event('input', { bubbles: true }));
        activeInput.dispatchEvent(new Event('change', { bubbles: true }));

        // update the visible span too
        if(displaySpan){
            displaySpan.textContent = String(h).padStart(2,'0') + ':' + String(m).padStart(2,'0') + ' ' + p;
        }

        // close box
        box.classList.add('tp-hidden');
    }

    // Parse a stored value (accepts "16:00", "04:00", "04:00 PM", "4:00 PM", etc.)
    function parseExisting(value){
        if(!value) return false;

        // trim
        value = String(value).trim();

        // try patterns:
        // 1) HH:MM (24-hour) e.g. 16:00
        let m24 = value.match(/^(\d{1,2}):(\d{2})$/);
        if(m24){
            let hour = parseInt(m24[1],10), minute = parseInt(m24[2],10);
            p = hour >= 12 ? 'PM' : 'AM';
            if(hour === 0) h = 12;
            else if(hour > 12) h = hour - 12;
            else h = hour;
            m = minute;
            return true;
        }

        // 2) h:mm AM/PM e.g. 04:00 PM or 4:00 pm
        let m12 = value.match(/^(\d{1,2}):(\d{2})\s*([AaPp][Mm])$/);
        if(m12){
            h = parseInt(m12[1],10);
            m = parseInt(m12[2],10);
            p = m12[3].toUpperCase();
            // sanitize h to be 1..12
            if(h <= 0) h = 12;
            if(h > 12) h = h % 12;
            return true;
        }

        return false;
    }

    // position floating box near input
    function positionBox(){
        if(!activeInput) return;
        const r = activeInput.getBoundingClientRect();
        const boxWidth = box.offsetWidth;
        const windowWidth = window.innerWidth;
        let left = r.left;
        if(left + boxWidth > windowWidth - 10) left = windowWidth - boxWidth - 10;
        if(left < 10) left = 10;
        box.style.top = r.bottom + window.scrollY + 8 + 'px';
        box.style.left = left + window.scrollX + 'px';
    }

    // create (or return existing) visual display span next to input
    function ensureDisplaySpan(input){
        // if already created for this input, return
        if(input._tpDisplay) return input._tpDisplay;

        // create span
        const span = document.createElement('span');
        span.className = 'tp-display-span';
        // basic inline style so it lines up — user can style with CSS if desired
        span.style.marginLeft = '8px';
        span.style.fontWeight = '600';
        span.style.fontSize = '0.95em';
        span.style.verticalAlign = 'middle';
        span.style.cursor = 'pointer';
        span.style.display = 'none';

        // put span after input
        input.parentNode.insertBefore(span, input.nextSibling);

        // clicking span should open the picker too
        span.addEventListener('click', (e) => {
            e.stopPropagation();
            input.click();
        });

        input._tpDisplay = span;
        return span;
    }

    // Initialize the custom picker behavior for an input element id
    window.initCustomTimePicker = function(id){
        const input = document.getElementById(id);
        if(!input || input._tp) return;
        input._tp = true;

        // Make input readonly (we control value via picker). But keep it focusable.
        input.readOnly = true;

        // Create and store display span (so users see 12-hour)
        displaySpan = ensureDisplaySpan(input);

        // set initial display based on existing input value
        if(input.value){
            if(parseExisting(input.value)){
                update();
            } else {
                // if unknown format, try to show something friendly
                displaySpan.textContent = input.value;
            }
        } else {
            // default display
            displaySpan.textContent = String(h).padStart(2,'0') + ':' + String(m).padStart(2,'0') + ' ' + p;
        }

        // clicking input opens the picker
        input.addEventListener('click', (ev) => {
            ev.stopPropagation();
            activeInput = input;
            displaySpan = input._tpDisplay; // refresh ref
            // parse the current stored value again (some flows set input.value programmatically)
            if(input.value) parseExisting(input.value);
            update();
            positionBox();
            box.classList.remove('tp-hidden');
        });

        // when the input loses (global clicks) we close — implemented in doc click below
    };

    // close when clicking outside
    document.addEventListener('click', (e)=>{
        if(!box.classList.contains('tp-hidden')){
            if(activeInput && (activeInput.contains(e.target) || (activeInput._tpDisplay && activeInput._tpDisplay.contains(e.target)))){
                // ignore clicks on the input or the display span
                return;
            }
            if(!box.contains(e.target)){
                box.classList.add('tp-hidden');
            }
        }
    });

    window.addEventListener('resize', ()=>{
        if(!box.classList.contains('tp-hidden')) positionBox();
    });
    window.addEventListener('scroll', ()=>{
        if(!box.classList.contains('tp-hidden')) positionBox();
    }, true);

})();
</script>

