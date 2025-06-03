document.addEventListener('DOMContentLoaded', () => {
  const filterButtons = document.querySelectorAll('.filter-btn');
  const serviceCards = Array.from(document.querySelectorAll('.service-card'));
  const paginationContainer = document.querySelector('.pagination');
  const itemsPerPage = 4; // số card mỗi trang

  let currentFilter = 'all';
  let currentPage = 1;
  let filteredCards = serviceCards;

  function showPage(page) {
    currentPage = page;
    const start = (page - 1) * itemsPerPage;
    const end = start + itemsPerPage;

    serviceCards.forEach(card => (card.style.display = 'none'));
    filteredCards.slice(start, end).forEach(card => (card.style.display = 'flex'));

    renderPagination();
  }

  function renderPagination() {
    const pageCount = Math.ceil(filteredCards.length / itemsPerPage);
    paginationContainer.innerHTML = '';

    if (filteredCards.length === 0) {
      paginationContainer.innerHTML = '<p class="no-results">Không có dịch vụ phù hợp.</p>';
      return;
    }

    if (pageCount <= 1) return;

    // Nút Previous
    const prevBtn = document.createElement('button');
    prevBtn.textContent = '‹';
    prevBtn.disabled = currentPage === 1;
    prevBtn.onclick = () => showPage(currentPage - 1);
    paginationContainer.appendChild(prevBtn);

    // Nút số trang
    for (let i = 1; i <= pageCount; i++) {
      const btn = document.createElement('button');
      btn.textContent = i;
      if (i === currentPage) btn.classList.add('active');
      btn.onclick = () => showPage(i);
      paginationContainer.appendChild(btn);
    }

    // Nút Next
    const nextBtn = document.createElement('button');
    nextBtn.textContent = '›';
    nextBtn.disabled = currentPage === pageCount;
    nextBtn.onclick = () => showPage(currentPage + 1);
    paginationContainer.appendChild(nextBtn);
  }

  filterButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      filterButtons.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      currentFilter = btn.getAttribute('data-filter');

      if (currentFilter === 'all') {
        filteredCards = serviceCards;
      } else {
        filteredCards = serviceCards.filter(
          card => card.getAttribute('data-category') === currentFilter
        );
      }
      showPage(1);
    });
  });

  // Hiển thị trang đầu tiên khi load trang
  showPage(1);
});
