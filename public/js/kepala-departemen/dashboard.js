function dashboard() {
    return {
        view: 'week',
        currentWeek: 'Memuat...',
        days: [],
        employees: [],
        shifts: [],
        isLoading: true,
        isSaving: false,
        isModalOpen: false,
        currentStartDate: '',
        currentPage: 1,
        lastPage: 1,

        summary: {
            totalKaryawan: 0,
            hadir: 0,
            terlambat: 0,
            izinCuti: 0
        },

        form: {
            user_id: '',
            shift_id: '',
            start_date: '',
            end_date: ''
        },

        init() {
            this.fetchData();
            this.fetchSummary();
        },

        async fetchSummary() {
            try {
                const response = await fetch('/kepala-departement/api/summary');
                if (!response.ok) throw new Error('Network response was not ok');
                const data = await response.json();
                this.summary = data;
            } catch (error) {
                console.error('Error fetching summary:', error);
            }
        },

        formatDate(date) {
            const d = new Date(date);
            let month = '' + (d.getMonth() + 1);
            let day = '' + d.getDate();
            const year = d.getFullYear();

            if (month.length < 2) month = '0' + month;
            if (day.length < 2) day = '0' + day;

            return [year, month, day].join('-');
        },

        nextWeek() {
            if (!this.currentStartDate) return;
            let [year, month, day] = this.currentStartDate.split('-');
            let d = new Date(year, month - 1, day);
            d.setDate(d.getDate() + 7);
            this.currentStartDate = this.formatDate(d);
            this.currentPage = 1;
            this.fetchData(this.currentPage);
        },

        prevWeek() {
            if (!this.currentStartDate) return;
            let [year, month, day] = this.currentStartDate.split('-');
            let d = new Date(year, month - 1, day);
            d.setDate(d.getDate() - 7);
            this.currentStartDate = this.formatDate(d);
            this.currentPage = 1;
            this.fetchData(this.currentPage);
        },

        changePage(page) {
            if (page >= 1 && page <= this.lastPage) {
                this.fetchData(page);
            }
        },

        async fetchData(page = 1) {
            this.isLoading = true;
            try {
                let url = `/kepala-departement/api/jadwal?page=${page}`;
                if (this.currentStartDate) {
                    let [year, month, day] = this.currentStartDate.split('-');
                    let start = new Date(year, month - 1, day);
                    let end = new Date(year, month - 1, day);
                    end.setDate(end.getDate() + 6);
                    url += `&start_date=${this.formatDate(start)}&end_date=${this.formatDate(end)}`;
                }

                const response = await fetch(url);
                if (!response.ok) throw new Error('Network response was not ok');

                const data = await response.json();
                this.employees = data.employees;
                this.shifts = data.shifts;
                if (data.pagination) {
                    this.currentPage = data.pagination.current_page;
                    this.lastPage = data.pagination.last_page;
                }

                let [year, month, day] = data.start_date.split('-');
                let start = new Date(year, month - 1, day);

                let [endYear, endMonth, endDay] = data.end_date.split('-');
                let end = new Date(endYear, endMonth - 1, endDay);

                const daysOfWeek = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
                const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];

                this.days = [];
                let tempDate = new Date(start);
                let today = new Date();
                today.setHours(0,0,0,0);

                for (let i = 0; i < 7; i++) {
                    this.days.push({
                        day: daysOfWeek[tempDate.getDay()],
                        date: tempDate.getDate(),
                        date_full: this.formatDate(tempDate),
                        active: tempDate.getTime() === today.getTime()
                    });
                    tempDate.setDate(tempDate.getDate() + 1);
                }

                this.currentWeek = `${start.getDate()} ${monthNames[start.getMonth()]} - ${end.getDate()} ${monthNames[end.getMonth()]} ${end.getFullYear()}`;
                this.currentStartDate = data.start_date;

            } catch (error) {
                console.error('Error fetching schedules:', error);
            } finally {
                this.isLoading = false;
            }
        },

        openModal(userId = '', startDate = '', endDate = '') {
            this.form.user_id = userId;
            this.form.shift_id = '';
            this.form.start_date = startDate;
            this.form.end_date = endDate;
            this.isModalOpen = true;
        },

        closeModal() {
            this.isModalOpen = false;
        },

        async submitSchedule() {
            if (!this.form.user_id || !this.form.shift_id || !this.form.start_date || !this.form.end_date) {
                alert('Mohon lengkapi semua data: Karyawan, Shift, Tanggal Mulai, dan Selesai!');
                return;
            }

            this.isSaving = true;
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            try {
                const response = await fetch('/kepala-departement/api/jadwal', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({
                        user_id: this.form.user_id,
                        shift_id: this.form.shift_id,
                        start_date: this.form.start_date,
                        end_date: this.form.end_date
                    })
                });

                if (!response.ok) {
                    const errData = await response.json();
                    throw new Error(errData.message || 'Gagal menyimpan jadwal');
                }

                alert('Jadwal berhasil ditambahkan!');
                this.closeModal();
                this.fetchData(this.currentPage); 
            } catch (error) {
                alert('Error: ' + error.message);
            } finally {
                this.isSaving = false;
            }
        },

        shiftClass(type) {
            if (!type) return 'bg-gray-100 text-gray-400';
            return {
                pagi: 'bg-emerald-100 text-emerald-700',
                siang: 'bg-yellow-100 text-yellow-700',
                sore: 'bg-orange-100 text-orange-700',
                malam: 'bg-indigo-100 text-indigo-700',
                libur: 'bg-red-100 text-red-600'
            }[type.toLowerCase()] || 'bg-gray-100 text-gray-600';
        }
    }
}
