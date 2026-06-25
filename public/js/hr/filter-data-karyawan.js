function vendorFilter() {
    return {
        vendors: [],
        isLoading: false,
        currentPage: 1,
        lastPage: 1,
        total: 0,
        startIndex: 0,
        endIndex: 0,

        fetchVendors() {
            this.isLoading = true;
            fetch(`/api/hr/vendors?page=${this.currentPage}`)
                .then(res => res.json())
                .then(data => {
                    this.vendors = data.data;
                    this.currentPage = data.current_page;
                    this.lastPage = data.last_page;
                    this.total = data.total;
                    this.startIndex = data.from || 0;
                    this.endIndex = data.to || 0;
                    this.isLoading = false;
                })
                .catch(err => {
                    console.error('Gagal mendapatkan data vendor :', err);
                    this.isLoading = false;
                });
        },

        changePage(page) {
            if (page >= 1 && page <= this.lastPage) {
                this.currentPage = page;
                this.fetchVendors();
            }
        },

        paginationRange() {
            let current = this.currentPage;
            let last = this.lastPage;
            let delta = 1;
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
        }
    }
}