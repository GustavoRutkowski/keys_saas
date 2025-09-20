type TAnyFunction = (...args: any[]) => any

// Returns a function copy with debounce.
function debounce<T extends TAnyFunction>(func: T, wait: number): (...args: Parameters<T>) => void {
	let timeout: NodeJS.Timeout | null = null;

	return function(this: any, ...args: Parameters<T>): void {
		const later = () => {
			timeout = null;
			func.apply(this, args);
		};
    
		if (timeout) clearTimeout(timeout);
		timeout = setTimeout(later, wait);
	};
}

export default debounce;
