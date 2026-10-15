// Shared by the Vue interface and the readable initial HTML export.
import { formatPrice, servicePricing } from './pricing.js'
const startingPrice = servicePricing['Interneto svetainių kūrimas'].amounts[0]
export const siteContent = {
    lt: {
        seoTitle: 'Svetainių ir el. parduotuvių kūrimas | DevSprendimai',
        seoDescription: `Interneto svetainių kūrimas nuo ${formatPrice(startingPrice)}, el. parduotuvės ir verslo sistemos. Individualus dizainas, mobili versija, techninis SEO. Kaune ir nuotoliu visoje Lietuvoje.`,
        headline: 'Interneto svetainių kūrimas',
        highlight: 'ir sprendimai verslui.',
        description: 'Kuriu interneto svetaines, el. parduotuves ir individualias verslo sistemas. Nuo aiškios struktūros ir dizaino iki programavimo, techninio SEO ir pagalbos po paleidimo.',
        aboutTitle: 'Su kuo dirbsite',
        aboutText: 'Esu Aivaras Razvodovskis, nepriklausomas svetainių ir verslo sistemų kūrėjas Kaune. Su įmonėmis ir individualiais klientais visoje Lietuvoje dirbu nuotoliu. Poreikius, sprendimą ir projekto eigą derinsite tiesiogiai su manimi.',
        pricingTitle: 'Svetainių kūrimo kainos.',
        pricingHighlight: 'Ir kiti sprendimai.',
        pricingIntro: 'Aiškus atspirties taškas planavimui. Galutinė kaina patvirtinama trumpai įvertinus projekto apimtį.',
        pricingNote: 'Nurodytos pradinės kainos. Domenas, talpinimas ir trečiųjų šalių licencijos vertinami atskirai. Galutinė kaina, taikomi mokesčiai, apimtis ir terminai patvirtinami pasiūlyme.',
    },
    en: {
        seoTitle: 'Website & e-commerce development | DevSprendimai',
        seoDescription: `Website development from ${formatPrice(startingPrice, true)}, online stores and custom business systems. Responsive design and technical SEO. Based in Kaunas, working remotely across Lithuania.`,
        headline: 'Website development',
        highlight: 'and solutions for business.',
        description: 'I build websites, online stores and custom business systems. From clear structure and design to development, technical SEO and support after launch.',
        aboutTitle: 'Who you will work with',
        aboutText: 'I am Aivaras Razvodovskis, an independent website and business systems developer based in Kaunas. I work remotely with companies and individual clients across Lithuania. You will discuss requirements, decisions and project progress directly with me.',
        pricingTitle: 'Website development prices.',
        pricingHighlight: 'And more ways to build.',
        pricingIntro: 'Clear starting points for planning. The final price is confirmed after a short scope review.',
        pricingNote: 'Prices are starting points. Domain registration, hosting and third-party licences are quoted separately. The final price, applicable taxes, scope and timeline are confirmed in your proposal.',
    },
}
