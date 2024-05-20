<template>
    <div class="modal" v-if="isVisible" @click="close">
        <div class="modal__container" @click.stop>
            <div class="modal__container-header">
                <button class="close-button" @click="close">&times;</button>
            </div>
            <div class="modal__container-body">
                <slot name="body"> Default body content </slot>
            </div>
            <div class="modal__container-footer">
                <slot name="footer"> </slot>
            </div>
        </div>
    </div>
</template>
<script>
import { defineComponent } from "vue";
export default defineComponent({
    props: {
        isVisible: {
            type: Boolean,
            default: false,
        },
    },
    setup(props, { emit }) {
        const close = () => {
            emit("close");
        };
        return {
            close,
        };
    },
});
</script>
