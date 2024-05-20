import { createRouter, createWebHistory } from "vue-router";
import auth from "./authRouter";
import calendar from "./calendarRouter";

const routes = [{ path: "/", redirect: "/login" }, ...auth, ...calendar];
const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
