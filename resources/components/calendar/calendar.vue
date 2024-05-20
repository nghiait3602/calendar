<template>
    <div class="calendar__body">
        <template v-for="(week, index) in monthMatrix" :key="index">
            <template v-if="index === 0">
                <template v-for="(day, index) in week" :key="index">
                    <div class="calendar__day">
                        <div class="calendar__day-mon">
                            {{ day.format("ddd").toUpperCase() }}
                        </div>
                        <span
                            class="calendar__day-text"
                            :class="{
                                'calendar__day-select':
                                    day.format('DD-MM-YYYY') === today,
                            }"
                            >{{ day.format("DD") }}</span
                        >
                    </div>
                </template>
            </template>
            <template v-else v-for="(day, index) in week" :key="index">
                <div class="calendar__day">
                    <span
                        class="calendar__day-text"
                        :class="{
                            'calendar__day-select':
                                day.format('DD-MM-YYYY') === today,
                        }"
                        >{{ day.format("DD") }}</span
                    >
                </div>
            </template>
        </template>
    </div>
</template>
<script>
import { defineComponent, ref, watchEffect } from "vue";
import { getMonth } from "../../utils/month";
import { useMonth } from "../../stores/curremtmonth";
import dayjs from "dayjs";
export default defineComponent({
    setup() {
        const test = dayjs().format("DD-MM-YYYY HH:mm:ss");
        console.log(test);
        const today = dayjs().format("DD-MM-YYYY");
        const stores = useMonth();
        const monthMatrix = ref(getMonth(stores.currentMonth));
        watchEffect(() => {
            monthMatrix.value = getMonth(stores.currentMonth);
        });
        return {
            monthMatrix,
            today,
        };
    },
});
</script>
