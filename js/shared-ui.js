// shared-ui.js
// Wspólna obsługa modala, przycisku mailto i scrollowania sekcji

document.addEventListener('DOMContentLoaded', function() {
  // Loading screen - hide after video loads and start video playback
  const loadingScreen = document.getElementById('loading-screen');
  const backgroundVideo = document.getElementById('background-video');
  if (loadingScreen && backgroundVideo) {
    // Pause video initially (prevent autoplay before loader hides)
    backgroundVideo.pause();
    
    backgroundVideo.addEventListener('loadeddata', function() {
      // Hide loading screen
      loadingScreen.style.opacity = '0';
      loadingScreen.style.transition = 'opacity 0.5s ease';
      setTimeout(() => {
        loadingScreen.style.display = 'none';
        // Start video playback AFTER loader is hidden
        backgroundVideo.play().catch(err => {
          console.log('Video autoplay prevented:', err);
        });
      }, 500);
    });
    
    // Fallback - hide after 3 seconds if video doesn't load
    setTimeout(() => {
      if (loadingScreen.style.display !== 'none') {
        loadingScreen.style.opacity = '0';
        loadingScreen.style.transition = 'opacity 0.5s ease';
        setTimeout(() => {
          loadingScreen.style.display = 'none';
          // Try to play video even if loading failed
          backgroundVideo.play().catch(err => {
            console.log('Video autoplay prevented:', err);
          });
        }, 500);
      }
    }, 3000);
  }

  // Obsługa przycisku Popros o wiecej informacji
  var btn = document.querySelector('button.btn-primary');
  if(btn) {
    btn.addEventListener('click', function() {
      window.location.href = 'mailto:tomestic@gmail.com';
    });
  }

  // Modal logic
  const modal = document.getElementById('imgModal');
  const modalImg = document.getElementById('modalImg');
  const closeModal = document.getElementById('closeModal');
  if (modal && modalImg && closeModal) {
    document.querySelectorAll('img.img-fluid, img.zoom-img').forEach(function(img) {
      img.addEventListener('click', function() {
        modalImg.src = this.src;
        modalImg.alt = this.alt;
        modal.classList.add('open');
      });
    });
    closeModal.addEventListener('click', function() {
      modal.classList.remove('open');
      setTimeout(() => { modalImg.src = ''; }, 400);
    });
    modal.addEventListener('click', function(e) {
      if (e.target === modal) {
        modal.classList.remove('open');
        setTimeout(() => { modalImg.src = ''; }, 400);
      }
    });
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        modal.classList.remove('open');
        setTimeout(() => { modalImg.src = ''; }, 400);
      }
    });
  }

  // Scroll navigation buttons logic
  const allSections = Array.from(document.querySelectorAll('section, .section-white'));
  const btnUp = document.getElementById('scrollUpBtn');
  const btnDown = document.getElementById('scrollDownBtn');
  if (btnUp && btnDown && allSections.length > 0) {
    function getCurrentSectionIdx() {
      const scroll = window.scrollY + window.innerHeight / 3;
      return allSections.findIndex(sec => {
        const rect = sec.getBoundingClientRect();
        const top = rect.top + window.scrollY;
        const bottom = top + rect.height;
        return scroll >= top && scroll < bottom;
      });
    }
    function updateNavBtns() {
      const idx = getCurrentSectionIdx();
      btnUp.hidden = !(idx > 0);
      btnDown.hidden = !(idx !== -1 && idx < allSections.length - 1);
    }
    function scrollToSection(idx) {
      if (idx >= 0 && idx < allSections.length) {
        allSections[idx].scrollIntoView({ behavior: 'smooth' });
      }
    }
    btnUp.addEventListener('click', function() {
      const idx = getCurrentSectionIdx();
      if (idx > 0) scrollToSection(idx - 1);
    });
    btnDown.addEventListener('click', function() {
      const idx = getCurrentSectionIdx();
      if (idx !== -1 && idx < allSections.length - 1) scrollToSection(idx + 1);
    });
    window.addEventListener('scroll', updateNavBtns);
    window.addEventListener('resize', updateNavBtns);
    setTimeout(updateNavBtns, 400);
  }
});
