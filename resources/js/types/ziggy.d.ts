import { Config, RouteParam, RouteParamsWithQueryOverload } from 'ziggy-js';

declare global {
    function route(): Config;
    function route(
        name: string,
        params?: RouteParamsWithQueryOverload | RouteParam,
        absolute?: boolean,
        config?: Config,
    ): string;
}

export {};