import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

document.addEventListener('DOMContentLoaded', () => {
    const tooltipOptions = {
        enabled: true,
        backgroundColor: '#db0505',
        titleColor: '#fff',
        bodyColor: '#fff',
        cornerRadius: 6,
        padding: 10,
        displayColors: false
    };

    // ✅ Visitas Semana
    new Chart(document.getElementById('visitsWeekChart'), {
        type: 'line',
        data: {
            labels: ["Seg","Ter","Qua","Qui","Sex","Sáb","Dom"],
            datasets: [{
                label:'Visitas',
                data:[120,150,180,200,170,190,210],
                borderColor:'#db0505',
                backgroundColor:'rgba(219,5,5,0.2)',
                tension:0.4,
                fill:true,
                pointRadius:6,
                pointBackgroundColor:'#db0505'
            }]
        },
        options:{ responsive:true, plugins:{ legend:{position:'top'}, tooltip:tooltipOptions }, scales:{ y:{ beginAtZero:true } } }
    });

    // ✅ Pedidos por Categoria
    new Chart(document.getElementById('ordersChart'), {
        type:'bar',
        data:{
            labels:["Bebidas","Salgados","Doces","Sanduíches"],
            datasets:[{ label:'Pedidos', data:[30,20,25,15], backgroundColor:['#db0505','#a73406','#f00505','#8d2626'] }]
        },
        options:{ responsive:true, plugins:{ legend:{ display:false }, tooltip:tooltipOptions }, scales:{ y:{ beginAtZero:true } } }
    });

    // ✅ Distribuição Produtos
    new Chart(document.getElementById('productsChart'), {
        type:'doughnut',
        data:{
            labels:["Bebidas","Salgados","Doces","Sanduíches"],
            datasets:[{ label:'Produtos', data:[40,20,25,15], backgroundColor:['#db0505','#a73406','#f00505','#8d2626'] }]
        },
        options:{ responsive:true, plugins:{ legend:{ position:'bottom' }, tooltip:tooltipOptions } }
    });

    // ✅ Últimas 12 horas (dinâmico)
    const ctxHours = document.getElementById('visitsHoursChart').getContext('2d');
    let hours = Array.from({length:12},(_,i)=>`${i+1}h`);
    let visitsData = Array.from({length:12},()=>Math.floor(Math.random()*50)+10);
    let ordersData = Array.from({length:12},()=>Math.floor(Math.random()*20)+5);

    const chartHours = new Chart(ctxHours, {
        type:'line',
        data:{
            labels: hours,
            datasets:[
                { label:'Visitas', data:visitsData, borderColor:'#db0505', backgroundColor:'rgba(219,5,5,0.2)', tension:0.4, fill:true },
                { label:'Pedidos', data:ordersData, borderColor:'#a73406', backgroundColor:'rgba(167,52,6,0.2)', tension:0.4, fill:true }
            ]
        },
        options:{ responsive:true, plugins:{ legend:{ position:'top' } } }
    });

    setInterval(()=>{
        hours.shift(); visitsData.shift(); ordersData.shift();
        const newHour = `${parseInt(hours[hours.length-1]||12)+1}h`;
        hours.push(newHour);
        visitsData.push(Math.floor(Math.random()*50)+10);
        ordersData.push(Math.floor(Math.random()*20)+5);
        chartHours.update();
    },10000);

});
