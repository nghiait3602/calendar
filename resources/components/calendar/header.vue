<template>
    <div class="calendar__header">
        <div class="header__left">
            <div class="header__left-btn-menu" @click="toggleMenu">
                <button class="header__button header__button-menu">
                    &#9776;
                </button>
                <img
                    class="calendar__logo"
                    src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS1RUn2OUUinbXcPXfUdYOvcRzFGG4rGuTf54qoXt8GIKNbO5Q1eTUWEk5uLc3qMBGzN4A&usqp=CAU"
                    alt="Calendar Logo"
                />
            </div>
            <span class="header__title">Lịch</span>
            <div class="header__btn">
                <div>
                    <button
                        class="header__button header__button-today"
                        @click="goToToday"
                    >
                        Hôm nay
                    </button>
                    <button
                        class="header__button header__button-previous"
                        @click="goToPreviousMonth"
                    >
                        <font-awesome-icon :icon="['fas', 'chevron-left']" />
                    </button>
                    <button
                        class="header__button header__button-next"
                        @click="goToNextMonth"
                    >
                        <font-awesome-icon :icon="['fas', 'chevron-right']" />
                    </button>
                </div>
                <span class="header__month">
                    Tháng {{ month + 1 }}, {{ year }}
                </span>
            </div>
        </div>
        <div class="header__right">
            <div class="header__dropdown">
                <select
                    id="monthSelect"
                    class="header__list"
                    @change="changeViewOptions"
                >
                    <option class="header__list-item" value="dayGridMonth">
                        Tháng
                    </option>
                    <option class="header__list-item" value="timeGridWeek">
                        Tuần
                    </option>
                    <option class="header__list-item" value="timeGridDay">
                        Ngày
                    </option>
                </select>
            </div>
            <span class="header__name"> {{ email }} </span>
            <div class="header__right-btn">
                <img
                    src="https://plus.unsplash.com/premium_photo-1669055554327-ae713c9c5800?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxlZGl0b3JpYWwtZmVlZHwxfHx8ZW58MHx8fHx8"
                    class="header__right-avatar"
                    alt="User Avatar"
                    @click="toggleLogoutButton()"
                />
                <div class="header__right-btn-down">
                    <button
                        class="logout-button"
                        v-if="showLogoutButton"
                        @click="logout()"
                    >
                        Đăng xuất
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { defineComponent, ref, watchEffect, reactive, toRefs } from "vue";
import { useRouter } from "vue-router";
import { storeToRefs } from "pinia";
import { useMonth, useMenu, useToDay } from "../../stores/curremtmonth";

export default defineComponent({
    props: {
        email: ref(),
        currentMonth: ref(),
        currentYear: ref(),
        changeView: Function,
    },
    setup(props, { emit }) {
        const showLogoutButton = ref(false);
        const router = useRouter();
        const stores = useMonth();
        const showMenu = useMenu();
        const today = useToDay();
        const date = reactive({
            month: props.currentMonth,
            year: props.currentYear,
        });
        const toggleMenu = () => {
            const show = !showMenu.menu;
            showMenu.onMenu(show);
            console.log(show);
            emit("onShowMenu");
        };
        watchEffect(() => {
            date.month = props.currentMonth;
            date.year = props.currentYear;
        });
        const toggleLogoutButton = () => {
            showLogoutButton.value = !showLogoutButton.value;
        };
        const goToPreviousMonth = () => {
            emit("prev");
        };
        const goToNextMonth = () => {
            emit("next");
        };
        const goToToday = () => {
            emit("today");
            let temp = !today.today;
            today.onToDay(temp);
        };
        const changeViewOptions = (event) => {
            props.changeView(event.target.value);
        };
        const logout = () => {
            localStorage.removeItem("token");
            router.push({ name: "auth-login" });
        };
        return {
            toggleMenu,
            toggleLogoutButton,
            logout,
            goToPreviousMonth,
            goToNextMonth,
            goToToday,
            showMenu,
            showLogoutButton,
            ...storeToRefs(stores),
            ...toRefs(date),
            changeViewOptions,
        };
    },
});
</script>
