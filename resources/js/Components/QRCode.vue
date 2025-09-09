<template>
    <div class="qr-code-container">
        <canvas ref="qrCanvas" class="qr-canvas"></canvas>
        <div class="mt-4 flex gap-2 justify-center">
            <button 
                @click="downloadQR" 
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
            >
                Download
            </button>
            <button 
                @click="copyQRToClipboard" 
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
            >
                Copy
            </button>
        </div>
        
        <!-- Toast Notification -->
        <Toast 
            ref="toastRef"
            :message="toastMessage"
            :show="showToast"
            @close="showToast = false"
        />
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import QRCode from 'qrcode'
import Toast from './Toast.vue'

const props = defineProps({
    url: {
        type: String,
        required: true
    },
    size: {
        type: Number,
        default: 200
    },
    errorCorrectionLevel: {
        type: String,
        default: 'M',
        validator: (value) => ['L', 'M', 'Q', 'H'].includes(value)
    },
    margin: {
        type: Number,
        default: 4
    },
    color: {
        type: Object,
        default: () => ({
            dark: '#000000',
            light: '#FFFFFF'
        })
    }
})

const qrCanvas = ref(null)
const qrDataUrl = ref('')
const toastRef = ref(null)
const showToast = ref(false)
const toastMessage = ref('Copy to clipboard')

const generateQR = async () => {
    if (!qrCanvas.value || !props.url) return
    
    try {
        // High resolution multiplier for crisp rendering
        const pixelRatio = window.devicePixelRatio || 1
        const scaleFactor = 3 // Additional scale for ultra-high quality
        const highResSize = props.size * scaleFactor * pixelRatio
        
        // Set canvas internal resolution to high quality
        qrCanvas.value.width = highResSize
        qrCanvas.value.height = highResSize
        
        // Keep display size fixed at 200x200 pixels
        qrCanvas.value.style.width = '200px'
        qrCanvas.value.style.height = '200px'
        
        const options = {
            width: highResSize,
            margin: props.margin,
            color: props.color,
            errorCorrectionLevel: 'H', // Highest error correction for best quality
            rendererOpts: {
                quality: 1.0 // Maximum quality
            }
        }
        
        // Enable high-quality rendering
        const ctx = qrCanvas.value.getContext('2d')
        ctx.imageSmoothingEnabled = true
        ctx.imageSmoothingQuality = 'high'
        
        await QRCode.toCanvas(qrCanvas.value, props.url, options)
        
        // Store high-quality data URL for copying/downloading
        qrDataUrl.value = qrCanvas.value.toDataURL('image/png', 1.0)
    } catch (error) {
        console.error('Error generating QR code:', error)
    }
}

const downloadQR = () => {
    if (!qrDataUrl.value) return
    
    const link = document.createElement('a')
    link.download = 'qrcode.png'
    link.href = qrDataUrl.value
    link.click()
}

const copyQRToClipboard = async () => {
    if (!qrCanvas.value) return
    
    try {
        const blob = await new Promise(resolve => {
            qrCanvas.value.toBlob(resolve, 'image/png')
        })
        
        await navigator.clipboard.write([
            new ClipboardItem({ 'image/png': blob })
        ])
        
        // Show success toast notification
        toastMessage.value = 'QR Code copied to clipboard!'
        showToast.value = true
    } catch (error) {
        console.error('Failed to copy QR code:', error)
        // Show error toast notification
        toastMessage.value = 'Failed to copy QR Code'
        showToast.value = true
    }
}

// Generate QR code when component mounts
onMounted(() => {
    generateQR()
})

// Regenerate QR code when URL or size changes
watch([() => props.url, () => props.size], () => {
    generateQR()
})
</script>

<style scoped>
.qr-code-container {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.qr-canvas {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    width: 200px !important;
    height: 200px !important;
    object-fit: contain;
    image-rendering: -webkit-optimize-contrast;
    image-rendering: crisp-edges;
}
</style>