function karyawanTable() {
    return {
        employees: [],
        isLoading: false,
        currentPage: 1,
        lastPage: 1,
        startIndex: 1,
        vendorId: '',

        initComponent() {
            this.fetchData();

            // Listen to vendor filter updates from the filter component
            window.addEventListener('change', (e) => {
                if (e.target.name === 'vendor_id') {
                    this.vendorId = e.target.value;
                    this.currentPage = 1;
                    this.fetchData();
                }
            });
        },

        fetchData() {
            this.isLoading = true;
            
            let url = `/api/hr/karyawan?page=${this.currentPage}`;
            if (this.vendorId) {
                url += `&vendor_id=${this.vendorId}`;
            }

            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Gagal mengambil data');
                    }
                    return response.json();
                })
                .then(data => {
                    this.employees = data.data;
                    this.currentPage = data.current_page;
                    this.lastPage = data.last_page;
                    this.startIndex = data.from || 1;
                    this.isLoading = false;
                })
                .catch(error => {
                    console.error('Error fetching data karyawan:', error);
                    this.isLoading = false;
                });
        },

        changePage(page) {
            if (page >= 1 && page <= this.lastPage) {
                this.currentPage = page;
                this.fetchData();
            }
        },

        paginationRange() {
            let current = this.currentPage;
            let last = this.lastPage;
            let delta = 2;
            let left = current - delta;
            let right = current + delta;
            let range = [];

            if (left < 1) {
                right += (1 - left);
                left = 1;
            }
            
            if (right > last) {
                left -= (right - last);
                if (left < 1) left = 1;
                right = last;
            }

            for (let i = left; i <= right; i++) {
                range.push(i);
            }
            
            return range;
        },

        formatDate(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            const options = { day: '2-digit', month: 'short', year: 'numeric' };
            return date.toLocaleDateString('id-ID', options);
        }
    };
}
