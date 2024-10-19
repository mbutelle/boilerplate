import Axios, {type AxiosError, type AxiosRequestConfig} from 'axios';

const config = useRuntimeConfig();

export const AXIOS_INSTANCE = Axios.create({
    baseURL: config.public.API_URL,
});

export const CustomHttpClient = <T>(
    config: AxiosRequestConfig,
    options?: AxiosRequestConfig,
): Promise<T> => {
    const token = useCookie('token');

    if (token.value) {
        if (!config) {
            config = {};
        }

        if (!config.headers) {
            if (options?.headers) {
                config.headers = options.headers;
            } else {
                config.headers = {};
            }
        }

        config.headers.Authorization = ['Bearer', token.value].join(' ');
    }

    const source = Axios.CancelToken.source();
    const promise = AXIOS_INSTANCE({
            ...config,
            ...options,
            cancelToken: source.token,
        })
            .then(({data}) => data)
            .catch((error: AxiosError) => {
                if (401 === error.response?.status) {
                    token.value = null;
                    navigateTo('/login');
                }

            })
    ;

    // @ts-ignore
    promise.cancel = () => {
        source.cancel('Query was cancelled');
    };

    return promise;
};

export type BodyType<BodyData> = BodyData;

