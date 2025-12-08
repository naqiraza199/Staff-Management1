<div x-data x-init="
let id='{{ $fieldId }}', t=0;
(function wait(){
    const el=document.getElementById(id);
    if(el && window.initCustomTimePicker){
        window.initCustomTimePicker(id);
    }else if(t++<15){
        setTimeout(wait,120);
    }
})();
"></div>
