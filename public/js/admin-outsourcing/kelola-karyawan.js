function modalKaryawan() {
    return {
        search: '',
        modal: null,
        selected: {},
        isModalTambahOpen: false,
        departemens: [],
        form: {
            nip: '',
            nama_lengkap: '',
            email: '',
            nomor_tlp: '',
            alamat: '',
            departemen_id: ''
        },

        init() {
            this.fetchDepartemens();
        },

        async fetchDepartemens() {
            try {
                const response = await fetch('/admin-outsourcing/api/departemen');
                if (response.ok) {
                    this.departemens = await response.json();
                }
            } catch (err) {
                console.error("Gagal mengambil data departemen", err);
            }
        },

        async submitKaryawan() {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const response = await fetch('/admin-outsourcing/api/karyawan', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(this.form)
                });
                
                const responseData = await response.json();
                
                if (response.ok && responseData.success) {
                    alert(responseData.message);
                    
                    // Insert into local array so it shows up instantly
                    this.karyawans.unshift({
                        id: responseData.data.id_user,
                        nip: responseData.data.nip,
                        nama_lengkap: responseData.data.nama_lengkap,
                        email: responseData.data.email,
                        nomor_tlp: responseData.data.nomor_tlp,
                        alamat: responseData.data.alamat
                    });
                    
                    this.closeModalTambah();
                    
                    this.form = {
                        nip: '',
                        nama_lengkap: '',
                        email: '',
                        nomor_tlp: '',
                        alamat: '',
                        departemen_id: ''
                    };
                } else {
                    let errorMessage = responseData.message || 'Gagal mengajukan data';
                    if (responseData.errors) {
                        errorMessage += '\n' + Object.values(responseData.errors).flat().join('\n');
                    }
                    alert(errorMessage);
                }
            } catch (err) {
                console.error(err);
                alert("Terjadi kesalahan saat menghubungi server.");
            }
        },

        openModalTambah() {
            this.isModalTambahOpen = true;
        },

        closeModalTambah() {
            this.isModalTambahOpen = false;
        },

        karyawans: [{
            id: 1,
            nip: '2021001',
            nama_lengkap: 'Budi Santoso',
            email: 'budi@email.com',
            nomor_tlp: '0812',
            alamat: 'Jakarta'
        },
        {
            id: 2,
            nip: '2021002',
            nama_lengkap: 'Siti Rahayu',
            email: 'siti@email.com',
            nomor_tlp: '0823',
            alamat: 'Bandung'
        },
        {
            id: 3,
            nip: '2021003',
            nama_lengkap: 'Ahmad Fauzi',
            email: 'ahmad@email.com',
            nomor_tlp: '0834',
            alamat: 'Surabaya'
        },
        ],

        open(type, data) {
            this.modal = type
            this.selected = {
                ...data
            }
        },

        close() {
            this.modal = null
        },

        deleteData() {
            this.karyawans = this.karyawans.filter(k => k.id !== this.selected.id)
            this.close()
        },

        saveEdit() {
            const i = this.karyawans.findIndex(k => k.id === this.selected.id)
            if (i !== -1) this.karyawans[i] = {
                ...this.selected
            }
            this.close()
        },

        filteredKaryawans() {
            return this.karyawans.filter(k =>
                k.nama_lengkap.toLowerCase().includes(this.search.toLowerCase())
            )
        }
    }
}
