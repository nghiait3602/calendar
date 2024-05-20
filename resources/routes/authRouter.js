const auth = [
    {
        path: "/",
        component: () => import("../layout/calendar.vue"),
        beforeEnter(to, from, next) {
            const token = localStorage.getItem("token");
            // Nếu người dùng đã đăng nhập, chuyển hướng đến trang chính (calendar-home)
            if (token) {
                next({ name: "calendar-home" });
            } else {
                // Nếu người dùng chưa đăng nhập, cho phép hiển thị trang đăng nhập
                next();
            }
        },
        children: [
            {
                path: "login",
                name: "auth-login",
                component: () => import("../page/auth/index.vue"),
            },
            {
                path: "register",
                name: "auth-register",
                component: () => import("../page/auth/signup.vue"),
            },
        ],
    },
];
export default auth;
