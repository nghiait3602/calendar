import dayjs from "dayjs";

export function getMonth(month = dayjs().month()) {
    const year = dayjs().year();
    const fristDay = dayjs(new Date(year, month, 1)).day();
    let currentMonth = 0 - fristDay;
    const matrixDays = new Array(5).fill([]).map(() => {
        return new Array(7).fill(null).map(() => {
            currentMonth++;
            return dayjs(new Date(year, month, currentMonth));
        });
    });
    return matrixDays;
}
