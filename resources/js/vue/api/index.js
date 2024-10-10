import axios from 'axios';
import { ref } from 'vue';

const apiClient = axios.create({
    baseURL: 'http://localhost:80/api',
    headers: {
        'Content-Type': 'application/json',
    },
    withCredentials: true,
});

apiClient.interceptors.request.use(config => {
    const token = localStorage.getItem('token');
    if (token) {
        config.headers['Authorization'] = `Bearer ${token}`;
    }
    return config;
});

apiClient.interceptors.response.use(
    response => response,
    error => {
        if (error.response && error.response.data) {
            let errors = error.response.data;
            let success = [];
            if (errors && errors.errors) {
                if (errors.success) {
                    success = errors.success;
                }
                errors = errors.errors;
            }
            let componentInstance = null
            if ('vueComponentInstance' in error.config) {
                componentInstance = error.config.vueComponentInstance;
            }
            let errorsComp = null;
            if ('errors' in error.config) {
                errorsComp = error.config.errors;
            } else {
                errorsComp = ref(componentInstance.value.errors);
            }
            let successComp = null;
            if ('success' in error.config) {
                successComp = error.config.success;
            } else {
                successComp = ref(componentInstance.value.success);
            }
            Object.keys(errors).forEach((key) => {
                if (errorsComp) {
                    errorsComp.value[key] = errors[key];
                } else {
                    console.log('Error flashes ' + key);
                }
            });
            Object.keys(success).forEach((key) => {
                if (successComp) {
                    successComp.value[key] = success[key];
                } else {
                    console.log('Success flashes ' + key);
                }
            });
        } else {
            console.log(error);
        }
        return Promise.reject(error);
    }
);

export default apiClient;