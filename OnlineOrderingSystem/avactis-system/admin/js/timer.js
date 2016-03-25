var timers_on_page = new Array();
var active_timers = new Array();

function addTimer(container_id)
{
    timers_on_page[container_id] = 0;
    active_timers[container_id] = false;
};

function showTimer(container_id)
{
    _minutes = Math.floor(timers_on_page[container_id] / 60);
    _seconds = timers_on_page[container_id] % 60;
    
    timer_str = sprintf("%02d",_minutes)+':'+sprintf("%02d",_seconds);
    document.getElementById(container_id).innerHTML = timer_str;
};

function startTimer(container_id)
{
    active_timers[container_id] = true;
    setTimeout('incrementTimer(\''+container_id+'\')',1000);
};

function stopTimer(container_id)
{
    active_timers[container_id] = false;
};

function setTimer(container_id,value)
{
    timers_on_page[container_id] = value;
    showTimer(container_id);
};

function incrementTimer(container_id)
{
    if(active_timers[container_id])
    {
        timers_on_page[container_id]+=1;
        showTimer(container_id);
        setTimeout('incrementTimer(\''+container_id+'\')',1000);
    };
};