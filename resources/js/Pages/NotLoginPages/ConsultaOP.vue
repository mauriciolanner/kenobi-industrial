<template>
    <div class="container">
        <div class="row justify-content-center my-5">
            <div class="col-sm-12 col-md-8 col-lg-5 my-4">

                <div class="card bg-white card-login shadow-sm p-3">
                    <qrcode-stream :formats="formato" :track="paintOutline" @detect="onDetect"
                        @error="onError"></qrcode-stream>

                    <div v-if="validationSuccess" class="validation-success">This is a URL</div>
                    --{{ codigo }}--
                </div>
            </div>
        </div>
    </div>
</template>
  
  
<script>
import { defineComponent } from 'vue'
import { QrcodeStream, QrcodeDropZone, QrcodeCapture } from 'vue-qrcode-reader'
import { Head, Link } from '@inertiajs/inertia-vue3';

export default defineComponent({
    created: () => {
    },
    components: {
        Head,
        Link,
        QrcodeStream,
        QrcodeDropZone,
        QrcodeCapture
    },

    props: {},

    data() {
        return {
            codigo: '',
            formato: ["code_128"]
        }
    },
    methods: {
        paintOutline(detectedCodes, ctx) {
            for (const detectedCode of detectedCodes) {
                const [firstPoint, ...otherPoints] = detectedCode.cornerPoints

                ctx.strokeStyle = 'red'

                ctx.beginPath()
                ctx.moveTo(firstPoint.x, firstPoint.y)
                for (const { x, y } of otherPoints) {
                    ctx.lineTo(x, y)
                }
                ctx.lineTo(firstPoint.x, firstPoint.y)
                ctx.closePath()
                ctx.stroke()
            }
        },
        onDetect(detectedCodes) {
            this.codigo = detectedCodes;
        },
        onError(detectedCodes) {
            this.codigo = detectedCodes;
        },
    }
})
</script>
  