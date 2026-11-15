import {ErrorListProps} from '../types/index'

export default function ErrorList({ errors }: ErrorListProps) {
    if (!errors) return null;

    if (typeof errors === 'string') {
        return (
            <div className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {errors}
            </div>
        );
    }

    const errorMessages = Object.values(errors).flat();

    if (errorMessages.length === 0) return null;

    return (
        <div className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul className="list-disc list-inside space-y-1">
                {errorMessages.map((message, index) => (
                    <li key={index} className="text-sm">{message}</li>
                ))}
            </ul>
        </div>
    );
}