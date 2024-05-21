<template>
    <div class="calendar">
        <Header
            :email="email"
            @prev="goToPrev"
            @next="goToNext"
            @today="goToToday"
            @getDate="getDate"
            @onShowMenu="handleShowMenu"
            :changeView="changeView"
            :currentYear="currentYear"
            :currentMonth="currentMonth"
        />
        <div class="calendar__main">
            <Sidebar
                @dateClick="handleDateClick"
                :currentEvents="currentEvents"
            />

            <div class="calendar__body">
                <FullCalendar :options="calendarOptions" ref="fullCalendar" />
                <ModalContainerVue
                    :event="newEvent"
                    :isEdit="isEdit"
                    @createEvent="createEvent"
                    @update="update"
                    @getEventsUser="getEventsUser"
                    @deleteEvent="deleteEvent"
                />
            </div>
        </div>
    </div>
</template>
<script>
import axios from "axios";
import FullCalendar from "@fullcalendar/vue3";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from "@fullcalendar/interaction";
import timeGridPlugin from "@fullcalendar/timegrid";
import { defineComponent, watchEffect, ref, onMounted, watch } from "vue";
import Header from "../../components/calendar/header.vue";
import Sidebar from "../../components/calendar/sidebar.vue";
import ModalContainerVue from "../../components/modal/Modal-container.vue";
import { useShowModal } from "../../stores/curremtmonth";

export default defineComponent({
    components: {
        FullCalendar,
        Header,
        Sidebar,
        ModalContainerVue,
    },
    setup() {
        const token = localStorage.getItem("token");
        const email = ref();
        const idUser = ref();
        const fullCalendar = ref();
        const currentYear = ref();
        const currentMonth = ref();
        const currentEvents = ref([]);
        const showModal = useShowModal();
        const isEdit = ref(false);
        const newEvent = ref({
            id: "", // Unique ID
            title: "",
            start: "",
            end: "",
            allDay: "",
            backgroundColor: "", // đổi màu
            categoris_id: 1,
            user_id: idUser.value,
        });
        watchEffect(() => {
            if (showModal.show === false) {
                isEdit.value = false;
            }
            const getUser = async () => {
                try {
                    const res = await axios.get(
                        "http://127.0.0.1:8000/api/user",
                        {
                            headers: {
                                Authorization: `Bearer ${token}`,
                            },
                        }
                    );
                    email.value = res.data.email;
                    console.log(res.data.id);
                    idUser.value = res.data.id;
                } catch (error) {
                    console.log(error);
                }
            };
            getUser();
        });
        watch(idUser, (newVal) => {
            if (newVal !== null) {
                getEventsUser();
            }
        });
        const getEventsUser = async () => {
            try {
                const res = await axios.post(
                    `http://127.0.0.1:8000/api/user_event/${idUser.value}`,
                    {
                        headers: {
                            Authorization: `Bearer ${token}`,
                        },
                    }
                );
                const events = res.data.event.map((item) => ({
                    id: item.id.toString(),
                    title: item.name_event,
                    start: item.start_time,
                    end: item.end_time,
                    backgroundColor: item.color ? item.color : "blue", // Màu sự kiện
                    allDay: item.start_time.includes("T") ? false : true,
                }));
                fullCalendar.value.getApi().addEventSource(events);
                currentEvents.value = events;
            } catch (error) {
                console.log(error);
            }
        };
        const createEvent = async (data) => {
            const temp = {
                name_event: data._rawValue.title,
                start_time: data._rawValue.start,
                end_time: data._rawValue.end,
                user_id: idUser.value,
                notes: "SInh nhat",
                categoris_id: data._rawValue.categoris_id,
                color: data._rawValue.backgroundColor,
            };
            try {
                const res = await axios.post(
                    "http://127.0.0.1:8000/api/create",
                    temp,
                    {
                        headers: {
                            Authorization: `Bearer ${token}`,
                        },
                    }
                );
                calendarApi.addEvent(data._rawValue);
                getEventsUser();
            } catch (error) {}
        };
        const update = async (data) => {
            const temp = {
                name_event: data._rawValue.title,
                start_time: data._rawValue.start,
                end_time: data._rawValue.end,
                notes: "SInh nhat",
                categoris_id: 2,
                color: data._rawValue.backgroundColor,
            };
            try {
                const res = await axios.put(
                    `http://127.0.0.1:8000/api/event_update/${data._rawValue.id}`,
                    temp,
                    {
                        headers: {
                            Authorization: `Bearer ${token}`,
                        },
                    }
                );
            } catch (error) {}
        };
        const deleteEvent = async (data) => {
            try {
                const res = await axios.delete(
                    `http://127.0.0.1:8000/api/event_delete/${data._rawValue.id}`,
                    {
                        headers: {
                            Authorization: `Bearer ${token}`,
                        },
                    }
                );
            } catch (error) {}
        };
        onMounted(() => {
            if (fullCalendar.value) {
                getDate();
            }
        });

        // handler Calendar
        const goToPrev = () => {
            if (fullCalendar.value) {
                fullCalendar.value.getApi().prev();
                getDate();
            }
        };
        const goToNext = () => {
            if (fullCalendar.value) {
                fullCalendar.value.getApi().next();
                getDate();
            }
        };
        const goToToday = () => {
            if (fullCalendar.value) {
                fullCalendar.value.getApi().today();
                getDate();
            }
        };
        const handleShowMenu = () => {
            setTimeout(() => {
                console.log("handleShowMenu");
                fullCalendar.value.getApi().render();
            }, 500);
        };
        // change view
        const changeView = (viewName) => {
            if (fullCalendar.value) {
                fullCalendar.value.getApi().changeView(viewName);
            }
        };
        const getDate = () => {
            if (fullCalendar.value) {
                currentYear.value = parseInt(
                    fullCalendar.value.getApi().getDate().getFullYear()
                );
                console.log(fullCalendar.value.getApi().getEvents());
                currentMonth.value = parseInt(
                    fullCalendar.value.getApi().getDate().getMonth()
                );
            }
        };
        // tạo  sự kiện
        const handleDateSelect = (selectInfo) => {
            showModal.show = !showModal.show;
            if (showModal.show) {
                let calendarApi = selectInfo.view.calendar;
                calendarApi.unselect();
                newEvent.value = {
                    id: "", // Unique ID
                    title: "",
                    start: selectInfo.startStr,
                    end: selectInfo.endStr,
                    allDay: selectInfo.allDay,
                    backgroundColor: "green", // đổi màu
                    categoris_id: "",
                    user_id: idUser.value,
                };
                console.log(selectInfo.startStr, selectInfo.endStr);
                // calendarApi.addEvent(newEvent);
            }
        };
        // click để xóa sự kiện
        const handleEventClick = (clickInfo) => {
            console.log(clickInfo);
            showModal.show = !showModal.show;
            newEvent.value = {
                id: clickInfo.event._def.publicId, // Unique ID
                title: clickInfo.event._def.title,
                start: clickInfo.event._instance.range.start,
                end: clickInfo.event._instance.range.end,
                allDay: clickInfo.event._def.allDay,
                backgroundColor: clickInfo.event._def.ui.backgroundColor, // đổi màu
                user_id: idUser.value,
            };
            isEdit.value = true;
        };
        const handleEvents = (events) => {
            currentEvents.value = events;
        };
        ///chuyển ngày theo calendar sidebar
        const handleDateClick = (date) => {
            fullCalendar.value.getApi().gotoDate(date);
        };
        const calendarOptions = {
            plugins: [dayGridPlugin, interactionPlugin, timeGridPlugin],
            headerToolbar: {
                left: "",
                center: "",
                right: "",
            },
            locale: "vi",
            initialView: "dayGridMonth",
            editable: false,
            selectable: true,
            selectMirror: true,
            dayMaxEvents: true,
            weekends: true,
            select: handleDateSelect,
            eventClick: handleEventClick,
        };

        return {
            email,
            calendarOptions,
            currentEvents,
            fullCalendar,
            goToPrev,
            goToNext,
            goToToday,
            getDate,
            changeView,
            currentMonth,
            currentYear,
            handleDateClick,
            newEvent,
            createEvent,
            getEventsUser,
            isEdit,
            update,
            deleteEvent,
            handleShowMenu,
        };
    },
});
</script>
