<template>
    <div class="sidebar" :class="{ sidebar__none: show }">
        <div class="sidebar__add" :class="{ none: show }" @click="openModal">
            <svg width="36" height="36" viewBox="0 0 36 36">
                <path fill="#34A853" d="M16 16v14h4V20z"></path>
                <path fill="#4285F4" d="M30 16H20l-4 4h14z"></path>
                <path fill="#FBBC05" d="M6 16v4h10l4-4z"></path>
                <path fill="#EA4335" d="M20 16V6h-4v14z"></path>
                <path fill="none" d="M0 0h36v36H0z"></path>
            </svg>
            <span>Tạo</span>
        </div>
        <div class="sidebar__calendar" :class="{ none: show }">
            <div class="sidebar__calendar-header">
                <span class="sidebar__calendar-header-month">
                    Tháng
                    {{ currentMonth + 1 }}, {{ currentYear }}
                </span>
                <div class="sidebar__calendar-header-button">
                    <button
                        class="sidebar__button-previous"
                        @click="goToPreviousMonth"
                    >
                        &#8249;
                    </button>
                    <button class="sidebar__button-next" @click="goToNextMonth">
                        &#8250;
                    </button>
                </div>
            </div>
            <div class="calendar__small">
                <FullCalendar :options="calendarOptions" ref="fullCalendar" />
            </div>
        </div>
        <div class="sidebar__label">
            <div class="sidebar__label-header">
                <span class="sidebar__label-header__title">Lịch của tôi</span>
                <font-awesome-icon :icon="['fas', 'chevron-up']" />
            </div>
            <div class="sidebar__label-list">
                <div>
                    <label class="sidebar__label-item">
                        <input
                            id="event"
                            type="checkbox"
                            v-model="eventCheck"
                            class="sidebar__label-list__checkbox"
                            @change="handleCheckBox"
                        />
                        <div
                            class="checkbox__checkmark checkbox__checkmark-event"
                        ></div>
                        <div class="sidebar__label-list__label">Sự kiện</div>
                    </label>
                </div>
                <div>
                    <label class="sidebar__label-item">
                        <input
                            id="remin"
                            type="checkbox"
                            v-model="reminCheck"
                            class="sidebar__label-list__checkbox"
                            @change="handleCheckBox"
                        />
                        <div
                            class="checkbox__checkmark checkbox__checkmark-remin"
                        ></div>
                        <div class="sidebar__label-list__label">Lời nhắc</div>
                    </label>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import FullCalendar from "@fullcalendar/vue3";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from "@fullcalendar/interaction";
import { defineComponent, ref, watchEffect, onMounted, watch } from "vue";
import {
    useMonth,
    useMenu,
    useToDay,
    useShowModal,
} from "../../stores/curremtmonth";
import { storeToRefs } from "pinia";
import axios from "axios";

export default defineComponent({
    components: {
        FullCalendar,
    },
    props: {
        dateClick: Function,
        id: ref(),
    },
    setup(props, { emit }) {
        const fullCalendar = ref();
        const currentYear = ref();
        const currentMonth = ref();
        const stores = useMonth();
        const menu = useMenu();
        const useToday = useToDay();
        const today = ref(useToday.today);
        const show = ref(menu.menu);
        const useModal = useShowModal();
        const id = ref(props.id);
        const categoriId = ref({
            categoriId: null,
        });
        const eventCheck = ref(true);
        const reminCheck = ref(true);
        watchEffect(() => {
            id.value = props.id;
            show.value = menu.menu;
            today.value = useToday.today;
            if (today.value && show.value === false) {
                let temp = !useToday.today;
                if (fullCalendar.value) {
                    fullCalendar.value.getApi().today();
                    getDate();
                    useToday.onToDay(temp);
                }
            } else {
                useToday.onToDay(false);
            }
        });
        onMounted(() => {
            if (fullCalendar.value) {
                getDate();
            }
        });
        watch(id, (value) => {
            if (value !== null) {
                getAllEvent();
            }
        });
        const handleCheckBox = () => {
            if (eventCheck.value && reminCheck.value) {
                getAllEvent();
            } else if (reminCheck.value) {
                categoriId.value.categoriId = 2;
                getEvent(categoriId);
            } else if (eventCheck.value) {
                categoriId.value.categoriId = 1;
                getEvent(categoriId);
            } else {
                emit("removeAllEvents");
            }
        };
        const getEvent = async (categori) => {
            try {
                if (id.value) {
                    const res = await axios.post(
                        `http://127.0.0.1:8000/api/user_event/${id.value}?categoriId=${categori.value.categoriId}`
                    );
                    const events = res.data.event.map((item) => ({
                        id: item.id.toString(),
                        title: item.name_event,
                        start: item.start_time,
                        end: item.end_time,
                        backgroundColor: item.color ? item.color : "blue", // Màu sự kiện
                        allDay: item.start_time.includes("T") ? false : true,
                    }));
                    emit("addEventSource", events);
                }
            } catch (error) {
                console.log(error);
            }
        };
        const getAllEvent = async () => {
            try {
                if (id.value) {
                    const res = await axios.post(
                        `http://127.0.0.1:8000/api/user_event/${id.value}`
                    );
                    const events = res.data.event.map((item) => ({
                        id: item.id.toString(),
                        title: item.name_event,
                        start: item.start_time,
                        end: item.end_time,
                        backgroundColor: item.color ? item.color : "blue", // Màu sự kiện
                        allDay: item.start_time.includes("T") ? false : true,
                    }));
                    console.log(events);
                    emit("addEventSource", events);
                }
            } catch (error) {
                console.log(error);
            }
        };
        const openModal = () => {
            const show = !useModal.show;
            useModal.onShowModal(show);
            console.log(useModal.show);
        };
        const goToPreviousMonth = () => {
            if (fullCalendar.value) {
                fullCalendar.value.getApi().prev();
                getDate();
            }
        };
        const goToNextMonth = () => {
            if (fullCalendar.value) {
                fullCalendar.value.getApi().next();
                getDate();
            }
        };
        const getDate = () => {
            if (fullCalendar.value) {
                currentYear.value = parseInt(
                    fullCalendar.value.getApi().getDate().getFullYear()
                );
                currentMonth.value = parseInt(
                    fullCalendar.value.getApi().getDate().getMonth()
                );
            }
        };
        const dateClicked = (info) => {
            emit("dateClick", info.dateStr);
        };
        const calendarOptions = {
            plugins: [dayGridPlugin, interactionPlugin],
            headerToolbar: {
                left: "",
                center: "",
                right: "",
            },
            locale: "vi",
            initialView: "dayGridMonth",
            editable: true,
            selectable: true,
            selectMirror: true,
            dayMaxEvents: true,
            weekends: true,
            dateClick: dateClicked,
        };
        return {
            ...storeToRefs(stores),
            ...storeToRefs(menu),
            show,
            currentYear,
            currentMonth,
            goToPreviousMonth,
            goToNextMonth,
            calendarOptions,
            fullCalendar,
            openModal,
            eventCheck,
            reminCheck,
            handleCheckBox,
        };
    },
});
</script>
