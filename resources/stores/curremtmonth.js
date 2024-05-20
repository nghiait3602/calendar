import { defineStore } from "pinia";
import dayjs from "dayjs";
export const useMonth = defineStore("monthId", {
    state: () => ({
        currentMonth: dayjs().month(),
    }),
    actions: {
        onMonth(data) {
            this.currentMonth = data;
        },
    },
});

export const useMenu = defineStore("menuId", {
    state: () => ({
        menu: false,
    }),
    actions: {
        onMenu(data) {
            this.menu = data;
        },
    },
});
export const useToDay = defineStore("todayId", {
    state: () => ({
        today: false,
    }),
    actions: {
        onToDay(data) {
            this.today = data;
        },
    },
});
export const useShowModal = defineStore("modalId", {
    state: () => ({
        show: false,
    }),
    actions: {
        onShowModal(data) {
            this.show = data;
        },
    },
});
