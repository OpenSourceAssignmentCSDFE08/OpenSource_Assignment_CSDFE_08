// SIEMS — frontend logic
(function(){
  // Theme toggle
  const themeBtn = document.getElementById('themeToggle');
  const saved = localStorage.getItem('siems-theme') || 'dark';
  document.documentElement.setAttribute('data-bs-theme', saved);
  themeBtn?.addEventListener('click', () => {
    const next = document.documentElement.getAttribute('data-bs-theme')==='dark'?'light':'dark';
    document.documentElement.setAttribute('data-bs-theme', next);
    localStorage.setItem('siems-theme', next);
  });
  document.getElementById('sidebarToggle')?.addEventListener('click', () =>
    document.getElementById('sidebar')?.classList.toggle('open'));

  // Animate counters
  document.querySelectorAll('[data-count]').forEach(el => {
    const target = +el.dataset.count; const dur = 900; const start = performance.now();
    const step = t => { const p = Math.min(1,(t-start)/dur); el.textContent = Math.floor(p*target).toLocaleString(); if (p<1) requestAnimationFrame(step); };
    requestAnimationFrame(step);
  });

  // Dashboard charts
  if (window.SIEMS_DATA && window.Chart) {
    const d = window.SIEMS_DATA;
    const cssVar = n => getComputedStyle(document.documentElement).getPropertyValue(n).trim();
    Chart.defaults.color = cssVar('--muted'); Chart.defaults.borderColor = cssVar('--line');

    new Chart(document.getElementById('chartSeverity'), {
      type:'doughnut',
      data:{labels:['High','Medium','Low'],
        datasets:[{data:[d.severity.High,d.severity.Medium,d.severity.Low],
        backgroundColor:['#ff4d6d','#ffcc00','#22c55e'],borderWidth:0}]},
      options:{plugins:{legend:{position:'bottom'}}}
    });

    new Chart(document.getElementById('chartDay'), {
      type:'line',
      data:{labels:d.byDay.map(x=>x.d), datasets:[{label:'Incidents',
        data:d.byDay.map(x=>+x.c), borderColor:'#00ff9c',
        backgroundColor:'rgba(0,255,156,.15)', fill:true, tension:.35}]},
      options:{plugins:{legend:{display:false}}}
    });

    const kwLabels = Object.keys(d.keywords); const kwData = Object.values(d.keywords);
    new Chart(document.getElementById('chartKw'), {
      type:'bar',
      data:{labels:kwLabels, datasets:[{label:'Hits', data:kwData, backgroundColor:'#22d3ee'}]},
      options:{plugins:{legend:{display:false}}, indexAxis:'y'}
    });

    new Chart(document.getElementById('chartSrc'), {
      type:'polarArea',
      data:{labels:d.sources.map(x=>x.src), datasets:[{data:d.sources.map(x=>+x.c),
        backgroundColor:['#00ff9c','#22d3ee','#60a5fa','#ffcc00','#ff4d6d','#a78bfa']}]}
    });

    // Mini recent incidents
    fetch('ajax_incidents.php?per_page=5').then(r=>r.json()).then(j=>{
      const html = renderTable(j, {mini:true});
      document.getElementById('incidentsMini').innerHTML = html;
    });
  }

  // Incidents page
  if (window.INCIDENTS_PAGE) {
    const form = document.getElementById('filterForm');
    let state = {page:1, sort:'detected_at', dir:'desc'};
    const load = () => {
      const fd = new FormData(form);
      const params = new URLSearchParams();
      fd.forEach((v,k)=>{ if(v) params.set(k,v); });
      Object.entries(state).forEach(([k,v])=>params.set(k,v));
      document.getElementById('incidentsTable').innerHTML = '<div class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm"></div> Loading...</div>';
      fetch('ajax_incidents.php?'+params.toString()).then(r=>r.json()).then(j=>{
        document.getElementById('incidentsTable').innerHTML = renderTable(j, {});
        bindTableEvents(load, state);
      });
    };
    form.addEventListener('submit', e=>{e.preventDefault(); state.page=1; load();});
    form.addEventListener('reset', ()=>{ setTimeout(()=>{state.page=1; load();},0);});
    load();
  }

  function sevBadge(s){ return `<span class="badge bg-${({High:'danger',Medium:'warning',Low:'success'})[s]||'secondary'}">${s}</span>`; }
  function stBadge(s){ return `<span class="badge bg-${({Open:'primary',Investigating:'warning',Resolved:'success'})[s]||'secondary'}">${s}</span>`; }

  function renderTable(j, opt){
    if (!j.rows.length) return '<div class="text-center text-muted py-4">No incidents found</div>';
    const headers = [['incident_id','ID'],['sender_email','Sender'],['receiver_email','Receiver'],
      ['email_subject','Subject'],['severity_level','Severity'],['threat_score','Score'],
      ['status','Status'],['detected_at','Detected']];
    let h = '<div class="table-responsive"><table class="table align-middle"><thead><tr>';
    headers.forEach(([k,l])=> h += `<th class="sortable" data-sort="${k}" style="cursor:pointer">${l} <i class="bi bi-arrow-down-up small text-muted"></i></th>`);
    h += '<th></th></tr></thead><tbody>';
    j.rows.forEach(r => {
      h += `<tr>
        <td><a class="text-neon" href="incident_details.php?id=${r.incident_id}">#${r.incident_id}</a></td>
        <td>${escapeHtml(r.sender_email)}</td>
        <td>${escapeHtml(r.receiver_email)}</td>
        <td class="text-truncate" style="max-width:240px">${escapeHtml(r.email_subject||'')}</td>
        <td>${sevBadge(r.severity_level)}</td>
        <td>${r.threat_score}</td>
        <td>${stBadge(r.status)}</td>
        <td class="small text-muted">${r.detected_at}</td>
        <td><a class="btn btn-sm btn-outline-light" href="incident_details.php?id=${r.incident_id}"><i class="bi bi-eye"></i></a></td>
      </tr>`;
    });
    h += '</tbody></table></div>';
    if (!opt.mini) {
      h += `<div class="d-flex justify-content-between align-items-center mt-2">
        <small class="text-muted">Showing page ${j.page} of ${j.pages} · ${j.total} total</small>
        <ul class="pagination pagination-sm mb-0">`;
      for (let p=1; p<=j.pages; p++) {
        h += `<li class="page-item ${p===j.page?'active':''}"><a class="page-link page-btn" data-page="${p}" href="#">${p}</a></li>`;
      }
      h += `</ul></div>`;
    }
    return h;
  }

  function bindTableEvents(load, state){
    document.querySelectorAll('.page-btn').forEach(b => b.onclick = e => { e.preventDefault(); state.page = +b.dataset.page; load(); });
    document.querySelectorAll('.sortable').forEach(th => th.onclick = () => {
      const s = th.dataset.sort;
      if (state.sort===s) state.dir = state.dir==='asc'?'desc':'asc';
      else { state.sort = s; state.dir = 'asc'; }
      load();
    });
  }
  function escapeHtml(s){ return (s+'').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }
})();
