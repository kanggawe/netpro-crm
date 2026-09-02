/**
 * PPN Include & Exclude Calculation Engine (11%)
 */
function calculatePpn(nominal, isInclude) {
    let dpp = 0;
    let ppn = 0;
    let total = 0;

    nominal = parseFloat(nominal) || 0;

    if (isInclude) {
        dpp = Math.round(nominal / 1.11);
        ppn = nominal - dpp;
        total = nominal;
    } else {
        dpp = nominal;
        ppn = Math.round(nominal * 0.11);
        total = nominal + ppn;
    }

    return {
        dpp: dpp,
        ppn: ppn,
        total: total,
        modeText: isInclude ? 'Include PPN 11%' : 'Exclude PPN (+11%)'
    };
}
