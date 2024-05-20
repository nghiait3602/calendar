const calendar = [
    {
        path: "/",
        component: () => import("../layout/calendar.vue"),
        beforeEnter(to, from, next) {
            const token = localStorage.getItem("token");
            // Nếu người dùng đã đăng nhập, chuyển hướng đến trang chính (calendar-home)
            if (!token) {
                next({ name: "auth-login" });
            } else {
                // Nếu người dùng chưa đăng nhập, cho phép hiển thị trang đăng nhập
                next();
            }
        },
        children: [
            {
                path: "home",
                name: "calendar-home",
                component: () => import("../page/calendar/index.vue"),
            },
        ],
    },
];
export default calendar;
