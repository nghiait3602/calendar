<template>
    <form @submit.prevent="handlerLogin()">
        <div class="login">
            <h3 class="login__title">Login</h3>
            <span v-if="errors.email" class="login__error"
                >*{{ errors.email[0] }}</span
            >
            <input
                type="text"
                class="login__email"
                v-model="email"
                placeholder="Enter your email"
                :class="{ 'login__email--errors': errors.email }"
            />
            <span v-if="errors.password" class="login__error"
                >*{{ errors.password[0] }}</span
            >
            <input
                type="password"
                class="login__password"
                v-model="password"
                placeholder="Enter your password"
                :class="{ 'login__password--errors': errors.password }"
            />
            <button class="login__btn">Sign In</button>
            <span class="login__create"
                ><router-link
                    class="login__router"
                    :to="{ name: 'auth-register' }"
                    >Create your account?</router-link
                ></span
            >
        </div>
    </form>
</template>

<script>
import { defineComponent, reactive, toRefs, ref } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
export default defineComponent({
    setup() {
        const router = useRouter();
        const login = reactive({
            email: "",
            password: "",
        });
        const errors = ref({});
        // const token = localStorage.getItem("token");
        // if (token) router.push({ name: "calendar-home" });
        const handlerLogin = async () => {
            try {
                const res = await axios.post(
                    "http://127.0.0.1:8000/api/login",
                    login
                );
                localStorage.setItem("token", res.data.token);
                router.push({ name: "calendar-home" });
            } catch (error) {
                console.log(error);
                errors.value = error.response.data.errors;
            }
        };
        return {
            ...toRefs(login),
            handlerLogin,
            errors,
        };
    },
});
</script>
