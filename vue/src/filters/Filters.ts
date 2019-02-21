import moment from 'moment';

export function shortDate(value:string):string {
	/* return mysql server date as a readable format, without year */
	return moment(value).format("MMM D");
}

export function currency(value:number):string {
	/* add trailing zeroes if necessary */
	const num = value.toString().split(".");
	if (num[num.length - 1].length === 1) {
		num[num.length - 1] += "0";
	} else if (num[num.length - 1].length === 0) {
		num[num.length - 1] += "00";
	} else if (num.length === 1) {
		num[num.length - 1] += ".00";
		return num.join("");
	}
	return num.join(".");
}
