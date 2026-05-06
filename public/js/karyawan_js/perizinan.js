
// ============================================================
// SWITCH TAB TANGGAL
// ============================================================

let tabAktif = 'mulai'

function switchTab(tab) {
    if (tabAktif === tab) return
    tabAktif = tab

    const tabMulai = document.getElementById("tabMulai")
    const tabSelesai = document.getElementById("tabSelesai")
    const panelMulai = document.getElementById("slideCardMulai")
    const panelSelesai = document.getElementById("slideCardSelesai")
    const pill = document.getElementById("tabPill")

    if (tab === 'mulai') {
        // Geser pill ke kiri
        pill.style.transform = "translateX(0)"

        // Warna teks tab
        tabMulai.style.color = "#3C8B5E"
        tabSelesai.style.color = "rgba(255,255,255,0.7)"

        // Slide panel: mulai masuk dari kiri, selesai keluar ke kanan
        panelMulai.style.transform = "translateX(0)"
        panelMulai.style.opacity = "1"
        panelMulai.style.pointerEvents = "auto"

        panelSelesai.style.transform = "translateX(100%)"
        panelSelesai.style.opacity = "0"
        panelSelesai.style.pointerEvents = "none"

    } else {
        // Geser pill ke kanan
        pill.style.transform = "translateX(calc(100% + 0px))"

        // Warna teks tab
        tabSelesai.style.color = "#3C8B5E"
        tabMulai.style.color = "rgba(255,255,255,0.7)"

        // Slide panel: selesai masuk dari kanan, mulai keluar ke kiri
        panelSelesai.style.transform = "translateX(0)"
        panelSelesai.style.opacity = "1"
        panelSelesai.style.pointerEvents = "auto"

        panelMulai.style.transform = "translateX(-100%)"
        panelMulai.style.opacity = "0"
        panelMulai.style.pointerEvents = "none"
    }

    updateRingkasan()
}

function updateRingkasan() {
    const mulai = document.getElementById("tglMulai").value
    const selesai = document.getElementById("tglSelesai").value
    const box = document.getElementById("ringkasanTanggal")
    const teks = document.getElementById("teksRingkasan")

    if (mulai && selesai) {
        const diff = Math.round((new Date(selesai) - new Date(mulai)) / 86400000) + 1
        if (diff < 1) {
            teks.textContent = "⚠️ Tanggal selesai sebelum tanggal mulai"
            teks.className = "text-red-500"
        } else {
            teks.textContent = `${formatTglSingkat(mulai)}  →  ${formatTglSingkat(selesai)}  (${diff} hari)`
            teks.className = "text-gray-500"
        }
        box.classList.remove("hidden")
    } else if (mulai || selesai) {
        teks.textContent = mulai ?
            `Mulai: ${formatTglSingkat(mulai)} — belum ada tanggal selesai` :
            `Selesai: ${formatTglSingkat(selesai)} — belum ada tanggal mulai`
        teks.className = "text-gray-400"
        box.classList.remove("hidden")
    } else {
        box.classList.add("hidden")
    }
}

function formatTglSingkat(str) {
    if (!str) return "-"
    return new Date(str).toLocaleDateString("id-ID", {
        day: "numeric",
        month: "short",
        year: "numeric"
    })
}

// ============================================================
// DATA STORE (dummy — ganti dengan API call ke backend)
// ============================================================

let riwayatData = [{
    id: 1,
    tglMulai: "2026-04-01",
    keterangan: "Demam tinggi dan sakit kepala",
    namaFile: "surat_sakit_april.jpg",
    tipeFile: "image",
    fileData: null,
    status: "disetujui",
    tglPengajuan: "2026-04-01"
},
{
    id: 2,
    tglMulai: "2026-03-15",
    keterangan: "Maag kambuh",
    namaFile: "surat_sakit_maret.pdf",
    tipeFile: "pdf",
    fileData: null,
    status: "menunggu",
    tglPengajuan: "2026-03-15"
},
{
    id: 3,
    tglMulai: "2026-02-10",
    keterangan: "Flu dan batuk",
    namaFile: "surat_feb.jpg",
    tipeFile: "image",
    fileData: null,
    status: "ditolak",
    tglPengajuan: "2026-02-10"
}
]

let idCounter = 4
let fileSelected = null // file yang sedang dipilih di form utama
let editFileNew = null // file baru saat edit


// ============================================================
// RENDER RIWAYAT
// ============================================================

function renderRiwayat() {
    const filter = document.getElementById("filterStatus").value
    const container = document.getElementById("listRiwayat")
    const emptyState = document.getElementById("emptyState")

    const filtered = filter === "semua" ?
        riwayatData :
        riwayatData.filter(d => d.status === filter)

    if (filtered.length === 0) {
        container.innerHTML = ""
        emptyState.classList.remove("hidden")
        return
    }

    emptyState.classList.add("hidden")

    container.innerHTML = filtered.map(item => {

        const badgeClass = {
            menunggu: '<i class="fa-solid fa-hourglass-half mr-1"></i> Menunggu',
            disetujui: '<i class="fa-solid fa-circle-check mr-1"></i> Disetujui',
            ditolak: '<i class="fa-solid fa-circle-xmark mr-1"></i> Ditolak'
        }[item.status]

        const badgeLabel = {
            menunggu: " Menunggu",
            disetujui: " Disetujui",
            ditolak: " Ditolak"
        }[item.status]

        const fileIcon = item.tipeFile === "pdf" ?
            '<i class="fa-solid fa-file-pdf text-red-500"></i>' :
            '<i class="fa-solid fa-image text-emerald-500"></i>'

        const canEdit = item.status === "menunggu"

        return `
                    <div class="file-card border border-gray-100 rounded-xl p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between gap-3 flex-wrap">

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap mb-1">
                                    <span class="font-semibold text-gray-800 text-sm">
                                        ${formatTanggal(item.tglMulai)}
                                        ${item.tglSelesai && item.tglSelesai !== item.tglMulai ? ' — ' + formatTanggal(item.tglSelesai) : ''}
                                    </span>
                                    <span class="text-xs px-2 py-0.5 rounded-full font-medium ${badgeClass}">
                                        ${badgeLabel}
                                    </span>
                                </div>

                                <p class="text-gray-500 text-xs mb-2">Diajukan: ${formatTanggal(item.tglPengajuan)}</p>

                                ${item.keterangan
                ? `<p class="text-gray-600 text-sm mb-2">${item.keterangan}</p>`
                : ''}

                                <div class="flex items-center gap-2">
                                    <span class="text-sm">${fileIcon}</span>
                                    <span class="text-xs text-gray-500 truncate max-w-[180px]">${item.namaFile}</span>
                                    ${item.fileData
                ? `<button onclick="lihatFileRiwayat(${item.id})"
                                                class="text-xs text-emerald-600 hover:underline ml-1">
                                                <i class="fa-solid fa-eye"></i> Lihat
                                            </button>`
                : ''}
                                </div>
                            </div>

                            <!-- ACTION BUTTONS -->
                            <div class="flex gap-2 shrink-0">
                                ${canEdit ? `
                                    <button onclick="bukaEdit(${item.id})"
                                        class="text-xs bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-1.5 rounded-lg font-medium transition flex items-center gap-1">
                                        <i class="fa-solid fa-pen"></i> Edit
                                    </button>
                                ` : ''}
                                ${canEdit ? `
                                    <button onclick="bukaHapus(${item.id})"
                                        class="text-xs bg-red-50 text-red-500 hover:bg-red-100 px-3 py-1.5 rounded-lg font-medium transition flex items-center gap-1">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                `
    }).join("")
}


// ============================================================
// UPLOAD FILE — FORM UTAMA
// ============================================================

function handleFileSelect(input) {
    const file = input.files[0]
    if (!file) return
    if (!validasiFile(file)) return
    fileSelected = file
    tampilkanPreview(file)
}

function handleDragOver(e) {
    e.preventDefault()
    document.getElementById("dropZone").classList.add("drag-over")
}

function handleDragLeave() {
    document.getElementById("dropZone").classList.remove("drag-over")
}

function handleDrop(e) {
    e.preventDefault()
    document.getElementById("dropZone").classList.remove("drag-over")
    const file = e.dataTransfer.files[0]
    if (!file) return
    if (!validasiFile(file)) return
    fileSelected = file
    tampilkanPreview(file)
}

function tampilkanPreview(file) {
    const isPdf = file.type === "application/pdf"
    document.getElementById("previewIcon").textContent = isPdf ? "🗒️" : "🖼️"
    document.getElementById("namaFile").textContent = file.name
    document.getElementById("ukuranFile").textContent = formatUkuran(file.size)
    document.getElementById("filePreview").classList.remove("hidden")
}

function hapusFile() {
    fileSelected = null
    document.getElementById("fileSurat").value = ""
    document.getElementById("filePreview").classList.add("hidden")
}

function lihatFile() {
    if (!fileSelected) return
    const isPdf = fileSelected.type === "application/pdf"
    bukaModalPreview(fileSelected.name, isPdf, fileSelected)
}


// ============================================================
// UPLOAD FILE — MODAL EDIT
// ============================================================

function handleEditFileSelect(input) {
    const file = input.files[0]
    if (!file) return
    if (!validasiFile(file)) return
    editFileNew = file

    const isPdf = file.type === "application/pdf"
    document.getElementById("editNewPreviewIcon").textContent = isPdf ? "🗒️" : "🖼️"
    document.getElementById("editNamaFileBaru").textContent = file.name
    document.getElementById("editFilePreview").classList.remove("hidden")
}

function batalGantiSurat() {
    editFileNew = null
    document.getElementById("editFileSurat").value = ""
    document.getElementById("editFilePreview").classList.add("hidden")
}


// ============================================================
// SUBMIT PENGAJUAN BARU
// ============================================================

function submitPerizinan() {
    const btn = document.querySelector('[onclick="submitPerizinan()"]')
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Mengirim...'
    btn.disabled = true
    btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Kirim Pengajuan'
    btn.disabled = false
    const tglMulai = document.getElementById("tglMulai").value
    const tglSelesai = document.getElementById("tglSelesai").value
    const keterangan = document.getElementById("keterangan").value.trim()

    if (!tglMulai || !tglSelesai) {
        showToast("error", "Tanggal mulai dan selesai wajib diisi")
        return
    }

    if (new Date(tglSelesai) < new Date(tglMulai)) {
        showToast("error", "Tanggal selesai tidak boleh sebelum tanggal mulai")
        return
    }

    if (!fileSelected) {
        showToast("error", "Surat keterangan sakit wajib diunggah")
        return
    }

    // Baca file sebagai base64 untuk preview
    const reader = new FileReader()
    reader.onload = function (e) {

        const isPdf = fileSelected.type === "application/pdf"

        riwayatData.unshift({
            id: idCounter++,
            tglMulai,
            tglSelesai,
            keterangan,
            namaFile: fileSelected.name,
            tipeFile: isPdf ? "pdf" : "image",
            fileData: e.target.result,
            status: "menunggu",
            tglPengajuan: new Date().toISOString().split("T")[0]
        })

        renderRiwayat()
        resetForm()
        showToast("success", "Pengajuan berhasil dikirim!")
    }

    reader.readAsDataURL(fileSelected)
}

function resetForm() {
    document.getElementById("tglMulai").value = ""
    document.getElementById("tglSelesai").value = ""
    document.getElementById("keterangan").value = ""
    document.getElementById("ringkasanTanggal").classList.add("hidden")
    hapusFile()
    switchTab('mulai')
}


// ============================================================
// EDIT PENGAJUAN
// ============================================================

function bukaEdit(id) {
    const item = riwayatData.find(d => d.id === id)
    if (!item) return

    editFileNew = null

    document.getElementById("editId").value = id
    document.getElementById("editTglMulai").value = item.tglMulai
    document.getElementById("editKeterangan").value = item.keterangan || ""

    const isPdf = item.tipeFile === "pdf"
    document.getElementById("editPreviewIcon").textContent = isPdf ? "🗒️" : "🖼️"
    document.getElementById("editNamaFile").textContent = item.namaFile
    document.getElementById("editFilePreview").classList.add("hidden")
    document.getElementById("editFileSurat").value = ""

    bukaModal("modalEdit")
}

function simpanEdit() {
    const id = parseInt(document.getElementById("editId").value)
    const tglMulai = document.getElementById("editTglMulai").value
    const keterangan = document.getElementById("editKeterangan").value.trim()

    if (!tglMulai) {
        showToast("error", "Tanggal wajib diisi")
        return
    }

    const idx = riwayatData.findIndex(d => d.id === id)
    if (idx === -1) return

    const simpan = () => {
        riwayatData[idx].tglMulai = tglMulai
        riwayatData[idx].keterangan = keterangan
        renderRiwayat()
        tutupModal("modalEdit")
        showToast("success", "Pengajuan berhasil diperbarui!")
    }

    if (editFileNew) {
        const reader = new FileReader()
        reader.onload = function (e) {
            const isPdf = editFileNew.type === "application/pdf"
            riwayatData[idx].namaFile = editFileNew.name
            riwayatData[idx].tipeFile = isPdf ? "pdf" : "image"
            riwayatData[idx].fileData = e.target.result
            simpan()
        }
        reader.readAsDataURL(editFileNew)
    } else {
        simpan()
    }
}


// ============================================================
// HAPUS PENGAJUAN
// ============================================================

function bukaHapus(id) {
    document.getElementById("hapusId").value = id
    bukaModal("modalHapus")
}

function konfirmasiHapus() {
    const id = parseInt(document.getElementById("hapusId").value)
    riwayatData = riwayatData.filter(d => d.id !== id)
    renderRiwayat()
    tutupModal("modalHapus")
    showToast("success", "Pengajuan berhasil dihapus")
}


// ============================================================
// PREVIEW FILE DARI RIWAYAT
// ============================================================

function lihatFileRiwayat(id) {
    const item = riwayatData.find(d => d.id === id)
    if (!item || !item.fileData) return
    const isPdf = item.tipeFile === "pdf"
    bukaModalPreviewData(item.namaFile, isPdf, item.fileData)
}


// ============================================================
// MODAL HELPERS
// ============================================================

function bukaModalPreview(nama, isPdf, file) {
    document.getElementById("modalTitle").textContent = nama
    const imgEl = document.getElementById("previewImg")
    const pdfEl = document.getElementById("previewPdf")
    const dlLink = document.getElementById("downloadLink")

    if (isPdf) {
        imgEl.classList.add("hidden")
        pdfEl.classList.remove("hidden")
        const url = URL.createObjectURL(file)
        dlLink.href = url
        dlLink.download = nama
    } else {
        pdfEl.classList.add("hidden")
        imgEl.classList.remove("hidden")
        const reader = new FileReader()
        reader.onload = e => {
            imgEl.src = e.target.result
        }
        reader.readAsDataURL(file)
    }

    bukaModal("modalPreview")
}

function bukaModalPreviewData(nama, isPdf, dataUrl) {
    document.getElementById("modalTitle").textContent = nama
    const imgEl = document.getElementById("previewImg")
    const pdfEl = document.getElementById("previewPdf")
    const dlLink = document.getElementById("downloadLink")

    if (isPdf) {
        imgEl.classList.add("hidden")
        pdfEl.classList.remove("hidden")
        dlLink.href = dataUrl
        dlLink.download = nama
    } else {
        pdfEl.classList.add("hidden")
        imgEl.classList.remove("hidden")
        imgEl.src = dataUrl
    }

    bukaModal("modalPreview")
}

function bukaModal(id) {
    const el = document.getElementById(id)
    el.classList.remove("hidden")
    el.classList.add("flex")
}

function tutupModal(id) {
    const el = document.getElementById(id)
    el.classList.add("hidden")
    el.classList.remove("flex")
}


// ============================================================
// TOAST
// ============================================================

let toastTimeout

function showToast(tipe, pesan) {
    const toast = document.getElementById("toast")
    const toastIcon = document.getElementById("toastIcon")
    const toastMsg = document.getElementById("toastMsg")

    toast.className = `
fixed bottom-6 right-6 z-[200] flex items-center gap-3 px-5 py-3 min-w-[250px]
rounded-xl shadow-xl transition-all
${tipe === "success"
            ? "bg-emerald-50 border border-emerald-200"
            : "bg-red-50 border border-red-200"}
`

    toastIcon.innerHTML = tipe === "success" ?
        '<i class="fa-solid fa-circle-check text-emerald-600"></i>' :
        '<i class="fa-solid fa-circle-xmark text-red-500"></i>'
    toastMsg.textContent = pesan

    toast.classList.remove("hidden")
    toast.classList.add("flex")

    clearTimeout(toastTimeout)
    toastTimeout = setTimeout(() => {
        toast.classList.add("hidden")
        toast.classList.remove("flex")
    }, 3000)
}


// ============================================================
// UTILS
// ============================================================

function validasiFile(file) {
    const allowed = ["image/jpeg", "image/png", "application/pdf"]
    if (!allowed.includes(file.type)) {
        showToast("error", "Format file tidak didukung (JPG, PNG, PDF)")
        return false
    }
    if (file.size > 5 * 1024 * 1024) {
        showToast("error", "Ukuran file maksimal 5MB")
        return false
    }
    return true
}

function formatUkuran(bytes) {
    if (bytes < 1024) return bytes + " B"
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + " KB"
    return (bytes / (1024 * 1024)).toFixed(1) + " MB"
}

function formatTanggal(str) {
    if (!str) return "-"
    const d = new Date(str)
    return d.toLocaleDateString("id-ID", {
        day: "numeric",
        month: "long",
        year: "numeric"
    })
}

function hitungDurasi(tglMulai, tglSelesai) {
    const a = new Date(tglMulai)
    const b = new Date(tglSelesai)
    const hari = Math.round((b - a) / (1000 * 60 * 60 * 24)) + 1
    return hari === 1 ? "1 hari" : hari + " hari"
}


// ============================================================
// INIT
// ============================================================

window.addEventListener("load", function () {
    renderRiwayat()
})
