
export const toPersianNumber = (value: string | number | null | undefined): string => {
    if (value === null || value === undefined) return '';
    return String(value).replace(/\d/g, (d) => '۰۱۲۳۴۵۶۷۸۹'[parseInt(d)]);
};


export const toEnglishNumber = (value: string): string => {
    return value.replace(/[۰-۹]/g, (d) => String('۰۱۲۳۴۵۶۷۸۹'.indexOf(d)));
};