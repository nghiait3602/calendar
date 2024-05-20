<template>
    <div>
        <Modal :isVisible="showModal" @close="closeModal">
            <template #body>
                <div class="input__group">
                    <input
                        type="text"
                        v-model="eventData.title"
                        required
                        class="input__group-title"
                        placeholder="Thêm tiêu đề và thời gian"
                    />
                </div>
                <div class="button__group">
                    <button
                        @click="updateContent('1')"
                        class="button__group-event"
                        :class="{
                            'button__group-select': selectedOption === '1',
                        }"
                    >
                        Sự kiện
                    </button>
                    <button
                        ref="reminderButton"
                        @click="updateContent('2')"
                        class="button__group-event"
                        :class="{
                            'button__group-select': selectedOption === '2',
                        }"
                    >
                        Lời nhắc
                    </button>
                </div>

                <div v-if="selectedOption === '1'">
                    <input
                        type="text"
                        v-model="eventData.categoris_id"
                        style="display: none"
                        value="1"
                    />
                    <div>
                        <input
                            type="date"
                            v-model="eventData.start"
                            required
                            class="input__group-date"
                        />
                        <input
                            type="date"
                            v-model="eventData.end"
                            required
                            class="input__group-date"
                        />
                        <div class="color__picker">
                            <button
                                class="color__picker-item"
                                v-for="color in colors"
                                :key="color"
                                :style="{ backgroundColor: color }"
                                :class="{
                                    'color__picker-selected':
                                        eventData.backgroundColor === color,
                                }"
                                @click="selectColor(color)"
                            ></button>
                        </div>
                    </div>
                </div>

                <div v-if="selectedOption === '2'">
                    <input
                        style="display: none"
                        type="text"
                        v-model="eventData.categoris_id"
                        value="2"
                    />
                    <input
                        style="display: none"
                        type="datetime-local"
                        v-model="eventData.start"
                        required
                        class="input__group-date"
                    />
                    <input
                        type="datetime-local"
                        v-model="eventData.end"
                        required
                        class="input__group-date"
                    />
                </div>
            </template>
            <template #footer>
                <div class="button__group">
                    <button @click="deleteEvent" class="button__group-delete">
                        Xóa
                    </button>
                    <button @click="create" class="button__group-save">
                        Lưu
                    </button>
                </div>
            </template>
        </Modal>
    </div>
</template>
<script>
import { ref, watchEffect } from "vue";
import Modal from "./Modal.vue";
import { useShowModal } from "../../stores/curremtmonth";
import dayjs from "dayjs";
export default {
    components: {
        Modal,
    },
    props: {
        event: Object,
        isEdit: Boolean,
    },
    setup(props, { emit }) {
        const show = useShowModal();
        const showModal = ref(show.show);
        const selectedOption = ref("1");
        const eventData = ref({
            id: "", // Unique ID
            title: "",
            start: "",
            end: "",
            allDay: "",
            backgroundColor: "", // đổi màu
            categoris_id: "",
            user_id: "",
        });

        watchEffect(() => {
            showModal.value = show.show;
            const start = dayjs(props.event.start).format("YYYY-MM-DD");
            const end = dayjs(props.event.end).format("YYYY-MM-DD");
            if (start === end) {
                selectedOption.value = "2";
                eventData.value.start = dayjs(props.event.end).format(
                    "YYYY-MM-DDTHH:mm:ss"
                );
                eventData.value.end = dayjs(props.event.end).format(
                    "YYYY-MM-DDTHH:mm:ss"
                );
            } else {
                selectedOption.value = "1";
                eventData.value.start = dayjs(props.event.start).format(
                    "YYYY-MM-DD"
                );
                eventData.value.end = dayjs(props.event.end).format(
                    "YYYY-MM-DD"
                );
            }
            eventData.value = {
                ...eventData.value,
                id: props.event.id, // Unique ID
                title: props.event.title,
                allDay: props.event.allDay,
                backgroundColor: props.event.backgroundColor, // đổi màu
                categoris_id: props.event.categoris_id
                    ? props.event.categoris_id
                    : 1,
                user_id: props.event.user_id,
            };
        });

        const updateContent = (option) => {
            console.log(props.event.start);
            // const dateNow = new Date
            selectedOption.value = option;
            eventData.value.categoris_id = option;
            if (selectedOption.value === "2") {
                eventData.value.start = dayjs(props.event.start)
                    .set("hours", 2)
                    .format("YYYY-MM-DDTHH:mm:ss");
                console.log("eventData.value.start: ", eventData.value.start);
                eventData.value.end = dayjs(props.event.start)
                    .set("hours", 2)
                    .format("YYYY-MM-DDTHH:mm:ss");
            } else {
                eventData.value.start = dayjs(props.event.start).format(
                    "YYYY-MM-DD"
                );
                eventData.value.end = dayjs(props.event.end).format(
                    "YYYY-MM-DD"
                );
            }
        };
        const closeModal = () => {
            showModal.value = false;
            show.onShowModal(false);
        };
        const create = () => {
            if (props.isEdit) {
                emit("update", eventData);
                window.location.reload();
            } else {
                emit("createEvent", eventData);
                console.log(eventData.value);
                window.location.reload();
            }
            closeModal();
        };
        const deleteEvent = () => {
            emit("deleteEvent", eventData);
            window.location.reload();
            closeModal();
        };
        const colors = ["red", "blue", "green", "purple", "orange"];
        const selectColor = (color) => {
            eventData.value.backgroundColor = color;
        };
        return {
            showModal,
            closeModal,
            updateContent,
            selectedOption,
            eventData,
            create,
            deleteEvent,
            colors,
            selectColor,
        };
    },
};
</script>
