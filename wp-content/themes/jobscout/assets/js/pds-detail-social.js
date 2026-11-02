(function(){
  document.addEventListener('click',function(event){
    const trigger=event.target.closest('.pds-detail-share-trigger');
    if(trigger){
      const wrap=trigger.closest('.pds-detail-share-wrap');
      const menu=wrap.querySelector('.pds-detail-share-menu');
      const opening=menu.hidden;
      document.querySelectorAll('.pds-detail-share-menu').forEach(function(m){m.hidden=true;});
      document.querySelectorAll('.pds-detail-share-trigger').forEach(function(b){b.setAttribute('aria-expanded','false');});
      menu.hidden=!opening;
      trigger.setAttribute('aria-expanded',opening?'true':'false');
      return;
    }
    const copy=event.target.closest('.pds-detail-share-copy');
    if(copy){
      const url=copy.dataset.url;
      const fallback=function(){window.prompt('Copy article URL:',url);};
      if(navigator.clipboard && window.isSecureContext){
        navigator.clipboard.writeText(url).then(function(){
          const old=copy.textContent;
          copy.textContent='Copied!';
          setTimeout(function(){copy.textContent=old;},1800);
        }).catch(fallback);
      }else fallback();
      return;
    }
    if(!event.target.closest('.pds-detail-share-wrap')){
      document.querySelectorAll('.pds-detail-share-menu').forEach(function(m){m.hidden=true;});
      document.querySelectorAll('.pds-detail-share-trigger').forEach(function(b){b.setAttribute('aria-expanded','false');});
    }
  });
  document.addEventListener('keydown',function(e){
    if(e.key!=='Escape')return;
    document.querySelectorAll('.pds-detail-share-menu').forEach(function(m){m.hidden=true;});
    document.querySelectorAll('.pds-detail-share-trigger').forEach(function(b){b.setAttribute('aria-expanded','false');});
  });
})();
