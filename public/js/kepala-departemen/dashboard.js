function dashboard() {
    return {
        view: 'week',
        currentWeek: 'Memuat...',
        days: [],
        employees: [],
        allEmployees: [],
        employeeSearchQuery: '',
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
            user_ids: [],
            shift_id: '',
            start_date: '',
            end_date: ''
        },

        init() {
            this.fetchData();
            this.fetchSummary();
            this.fetchAllEmployees();
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

        async fetchAllEmployees() {
            try {
                const response = await fetch('/kepala-departement/api/karyawan-all');
                if (!response.ok) throw new Error('Failed to fetch employee list');
                this.allEmployees = await response.json();
            } catch (error) {
                console.error('Error fetching all employees:', error);
            }
        },

        filteredEmployees() {
            if (!this.employeeSearchQuery) {
                return this.allEmployees;
            }
            const query = this.employeeSearchQuery.toLowerCase();
            return this.allEmployees.filter(emp => 
                emp.nama_lengkap.toLowerCase().includes(query)
            );
        },

        allEmployeesSelected() {
            const filtered = this.filteredEmployees();
            if (filtered.length === 0) return false;
            return filtered.every(emp => this.form.user_ids.includes(emp.id_user));
        },

        toggleSelectAllEmployees() {
            const filtered = this.filteredEmployees();
            const filteredIds = filtered.map(emp => emp.id_user);
            
            if (this.allEmployeesSelected()) {
                // Deselect all filtered employees
                this.form.user_ids = this.form.user_ids.filter(id => !filteredIds.includes(id));
            } else {
                // Select all filtered employees
                filteredIds.forEach(id => {
                    if (!this.form.user_ids.includes(id)) {
                        this.form.user_ids.push(id);
                    }
                });
            }
        },

        getTodayDate() {
            const today = new Date();
            const yyyy = today.getFullYear();
            let mm = today.getMonth() + 1;
            let dd = today.getDate();

            if (mm < 10) mm = '0' + mm;
            if (dd < 10) dd = '0' + dd;

            return yyyy + '-' + mm + '-' + dd;
        },

        isPastDate(dateStr) {
            const today = this.getTodayDate();
            return dateStr < today;
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
            const todayStr = this.getTodayDate();

            if (endDate && endDate < todayStr) {
                window.dispatchEvent(new CustomEvent('flash-error', {
                    detail: { message: 'Seluruh tanggal pada minggu ini telah terlewati.' }
                }));
                return;
            }

            // Set user_ids
            if (userId) {
                this.form.user_ids = [userId];
            } else {
                this.form.user_ids = [];
            }

            this.form.shift_id = '';
            this.employeeSearchQuery = '';

            // Handle date bounds
            if (startDate) {
                this.form.start_date = startDate < todayStr ? todayStr : startDate;
            } else {
                this.form.start_date = todayStr;
            }

            if (endDate) {
                this.form.end_date = endDate < todayStr ? todayStr : endDate;
            } else {
                this.form.end_date = todayStr;
            }

            this.isModalOpen = true;
        },

        closeModal() {
            this.isModalOpen = false;
        },

        async submitSchedule() {
            const todayStr = this.getTodayDate();

            if (this.form.user_ids.length === 0) {
                window.dispatchEvent(new CustomEvent('flash-error', {
                    detail: { message: 'Mohon pilih minimal 1 karyawan!' }
                }));
                return;
            }
            if (!this.form.shift_id || !this.form.start_date || !this.form.end_date) {
                window.dispatchEvent(new CustomEvent('flash-error', {
                    detail: { message: 'Mohon lengkapi semua data: Shift, Tanggal Mulai, dan Selesai!' }
                }));
                return;
            }

            if (this.form.start_date < todayStr) {
                window.dispatchEvent(new CustomEvent('flash-error', {
                    detail: { message: 'Tanggal mulai tidak boleh sebelum hari ini!' }
                }));
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
                        user_ids: this.form.user_ids,
                        shift_id: this.form.shift_id,
                        start_date: this.form.start_date,
                        end_date: this.form.end_date
                    })
                });

                const resData = await response.json();

                if (!response.ok) {
                    throw new Error(resData.message || 'Gagal menyimpan jadwal');
                }

                window.dispatchEvent(new CustomEvent('flash-success', {
                    detail: { message: resData.message || 'Jadwal berhasil ditambahkan!' }
                }));

                this.closeModal();
                this.fetchData(this.currentPage);
                this.fetchSummary();
            } catch (error) {
                window.dispatchEvent(new CustomEvent('flash-error', {
                    detail: { message: 'Error: ' + error.message }
                }));
            } finally {
                this.isSaving = false;
            }
        },

        shiftClass(type) {
            if (!type) return 'bg-slate-100 text-slate-450';
            return {
                pagi: 'bg-emerald-50 text-emerald-700 border border-emerald-200/60',
                siang: 'bg-amber-50/80 text-amber-700 border border-amber-250',
                sore: 'bg-orange-50/80 text-orange-700 border border-orange-250',
                malam: 'bg-indigo-50 text-indigo-700 border border-indigo-250',
                libur: 'bg-rose-50/80 text-rose-600 border border-rose-250'
            }[type.toLowerCase()] || 'bg-slate-55/80 text-slate-700 border border-slate-200';
        }
    }
}

