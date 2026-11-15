import { format, parseISO } from 'date-fns-jalali';
import { faIR } from 'date-fns-jalali/locale';

export const formatToJalali = (dateString: string | null): string => {
  if (!dateString) return 'ندارد';

  try {
    if (dateString.includes(' ')) {
      const date = parseISO(dateString.replace(' ', 'T'));
      return format(date, 'd MMMM yyyy، ساعت HH:mm', { locale: faIR });
    }
    const date = parseISO(dateString);
    return format(date, 'd MMMM yyyy', { locale: faIR });
  } catch (error) {
    return dateString;
  }
};

export const formatToJalaliShort = (dateString: string | null): string => {
  if (!dateString) return '';

  try {
    const date = parseISO(dateString);
    return format(date, 'yyyy/MM/dd', { locale: faIR });
  } catch (error) {
    return '';
  }
};