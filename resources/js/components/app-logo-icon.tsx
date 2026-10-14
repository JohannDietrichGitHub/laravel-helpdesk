import type { SVGAttributes } from 'react';

export default function AppLogoIcon(props: SVGAttributes<SVGElement>) {
    return (
        <svg
            {...props}
            viewBox="0 0 40 40"
            xmlns="http://www.w3.org/2000/svg"
            aria-hidden="true"
        >
            <path
                fillRule="evenodd"
                clipRule="evenodd"
                d="M8 11.5A3.5 3.5 0 0 1 11.5 8h17a3.5 3.5 0 0 1 3.5 3.5v17a3.5 3.5 0 0 1-3.5 3.5h-17A3.5 3.5 0 0 1 8 28.5v-17Zm7 2.5a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm10 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4ZM13 23.5a7 7 0 0 0 14 0h-2a5 5 0 0 1-10 0h-2Z"
            />
        </svg>
    );
}
