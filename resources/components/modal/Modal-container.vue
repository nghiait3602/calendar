<template>
    <div>
        <Modal :isVisible="showModal" @close="closeModal">
            <template #body>
                <span class="error" v-if="eventData.title === ''"
                    >Không được để trống tiêu đề</span
                >
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
                        :disabled="eventData.categoris_id === '2' && isEdit"
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
                        :disabled="eventData.categoris_id === '1' && isEdit"
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
                            <input
                                class="color__picker-item"
                                type="color"
                                value="blue"
                                v-model="eventData.backgroundColor"
                            />
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
                        type="datetime-local"
                        v-model="eventData.start"
                        required
                        class="input__group-date"
                    />
                    <input
                        type="datetime-local"
                        v-model="eventData.end"
                        required
                        style="display: none"
                        class="input__group-date"
                    />
                </div>
            </template>
            <template #footer>
                <div class="button__group">
                    <button
                        @click="deleteEvent"
                        class="button__group-delete"
                        :disabled="eventData.id === ''"
                    >
                        Xóa
                    </button>
                    <button
                        @click="create"
                        class="button__group-save"
                        :disabled="eventData.title === ''"
                    >
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
                const day = new Date(props.event.end);
                eventData.value.start = dayjs(props.event.end)
                    .set("hours", day.getHours())
                    .set("minutes", day.getMinutes())
                    .format("YYYY-MM-DDTHH:mm:ss");
                eventData.value.end = dayjs(props.event.end)
                    .set("hours", day.getHours())
                    .set("minutes", day.getMinutes())
                    .format("YYYY-MM-DDTHH:mm:ss");
                eventData.value.categoris_id = "2";
            } else {
                selectedOption.value = "1";
                eventData.value.start = dayjs(props.event.start).format(
                    "YYYY-MM-DD"
                );
                eventData.value.end = dayjs(props.event.end).format(
                    "YYYY-MM-DD"
                );
                eventData.value.categoris_id = "1";
            }
            eventData.value = {
                ...eventData.value,
                id: props.event.id, // Unique ID
                title: props.event.title,
                allDay: props.event.allDay,
                backgroundColor: props.event.backgroundColor, // đổi màu
                user_id: props.event.user_id,
            };
        });

        const updateContent = (option) => {
            const today = new Date();
            console.log(props.event.start);
            selectedOption.value = option;
            eventData.value.categoris_id = option;
            if (selectedOption.value === "2") {
                eventData.value.start = dayjs(props.event.start)
                    .set("hours", today.getHours())
                    .format("YYYY-MM-DDTHH:mm:ss");
                eventData.value.end = dayjs(props.event.start)
                    .set("hours", today.getHours())
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
                // window.location.reload();
                emit("getEventsUser");
            } else {
                emit("createEvent", eventData);
                emit("getEventsUser");
                console.log(eventData.value);
                // window.location.reload();
            }
            closeModal();
        };
        const deleteEvent = () => {
            emit("deleteEvent", eventData);
            // window.location.reload();
            emit("getEventsUser");
            closeModal();
        };

        return {
            showModal,
            closeModal,
            updateContent,
            selectedOption,
            eventData,
            create,
            deleteEvent,
        };
    },
};
</script>
