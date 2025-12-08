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
    let activeInput = null;
    let h = 12, m = 0, p = 'AM';
    const mins=[0,15,30,45];

    const box=document.getElementById('tp-box');
    const hoursEl=document.getElementById('tp-hours');
    const minsEl=document.getElementById('tp-mins');
    const am=document.getElementById('tp-am');
    const pm=document.getElementById('tp-pm');

    for(let i=1;i<=12;i++){
        const d=document.createElement('div');
        d.textContent=String(i).padStart(2,'0');
        d.className='tp-slot';
        d.onclick=()=>{h=i;update();setValue()};
        hoursEl.appendChild(d);
    }

    mins.forEach(v=>{
        const d=document.createElement('div');
        d.textContent=String(v).padStart(2,'0');
        d.className='tp-slot';
        d.onclick=()=>{m=v;update();setValue()};
        minsEl.appendChild(d);
    });

    am.onclick=()=>{p='AM';update();setValue()}
    pm.onclick=()=>{p='PM';update();setValue()}

    function update(){
        [...hoursEl.children].forEach(e=>e.classList.toggle('active',+e.textContent===h));
        [...minsEl.children].forEach(e=>e.classList.toggle('active',+e.textContent===m));
        am.classList.toggle('active',p==='AM');
        pm.classList.toggle('active',p==='PM');
    }

    function to24(){
        if(p==='AM') return h===12?0:h;
        return h===12?12:h+12;
    }

    function setValue(){
        if(!activeInput)return;
        activeInput.value=String(h).padStart(2,'0')+':'+String(m).padStart(2,'0')+' '+p;
        activeInput.dispatchEvent(new Event('input',{bubbles:true}));
        activeInput.dispatchEvent(new Event('change',{bubbles:true}));
        box.classList.add('tp-hidden');
    }

    function positionBox(){
        if(!activeInput)return;
        const r=activeInput.getBoundingClientRect();
        const boxWidth=box.offsetWidth;
        const windowWidth=window.innerWidth;
        let left=r.left;
        if(left+boxWidth>windowWidth-10){
            left=windowWidth-boxWidth-10;
        }
        if(left<10){
            left=10;
        }
        box.style.top=r.bottom+window.scrollY+8+'px';
        box.style.left=left+window.scrollX+'px';
    }

    window.initCustomTimePicker=function(id){
        const input=document.getElementById(id);
        if(!input || input._tp)return;

        input._tp=true;
        input.type='text';
        input.readOnly=true;

        input.addEventListener('click',()=>{
            activeInput=input;
            positionBox();
            box.classList.remove('tp-hidden');
        });
    }

    document.addEventListener('click',(e)=>{
        if(!box.classList.contains('tp-hidden') && activeInput &&
           !activeInput.contains(e.target) &&
           !box.contains(e.target)){
            box.classList.add('tp-hidden');
        }
    });

    window.addEventListener('resize',()=>{
        if(!box.classList.contains('tp-hidden')){
            positionBox();
        }
    });
    window.addEventListener('scroll',()=>{
        if(!box.classList.contains('tp-hidden')){
            positionBox();
        }
    },true);

})();
</script>
