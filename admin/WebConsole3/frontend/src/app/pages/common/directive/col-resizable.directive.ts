import {Directive, DoCheck, ElementRef, EventEmitter, Input, OnDestroy, OnInit, Output, Renderer2} from '@angular/core';


class ResizeInfo {
  width: number;
  percent: number;
}

@Directive({
  selector: '[col-resizable]'
})
export class ColResizableDirective implements  OnInit, DoCheck {
  private el: ElementRef;
  private start: any;
  private pressed: boolean;
  private startX: number;
  private startWidth: any;

  @Input()
  elements: any[];

  @Output()
  onResize = new EventEmitter<ResizeInfo>();
  private elementProperty: string;


  constructor(el: ElementRef, private renderer: Renderer2) {
    this.el = el;

  }

  ngOnInit() {
    this.elementProperty = this.el.nativeElement.getAttribute('cr-update-property') || '';
  }
  ngDoCheck(): void {
    const table = this.el.nativeElement;
    const trs = table.getElementsByTagName('tr');
    let tds = null;

    for (let i = 0; i < trs.length; i++) {
      tds = trs[i].getElementsByTagName('td');
      if (tds.length > 0) {
        for (let n = 0; n < tds.length; n++) {

          if (tds[n].getElementsByClassName('resizer').length === 0) {
            const span = document.createElement('span');
            span.classList.add('resizer');
            tds[n].appendChild(span);
            span.addEventListener('mousedown', (event) => {
                this.start = event.target;
                this.pressed = true;
                this.startX = event.x;
                this.startWidth = this.start.parentElement.offsetWidth;
                this.initResizableColumns();
            });
          }
        }
      }
    }

  }

  private initResizableColumns() {
    this.renderer.listen('body', 'mousemove', (event) => {
      if (this.pressed) {
        const width = this.startWidth + (event.x - this.startX);
        this.start.parentElement.style.width = width + 'px';
        const element = this.elements[this.start.parentElement.cellIndex];
        if (typeof element !== 'undefined' && this.elementProperty !== ''){
          element[this.elementProperty] =  ((width / this.el.nativeElement.offsetWidth) * 100);
        }
        this.onResize.emit(element);
      }
    })
    this.renderer.listen('body', 'mouseup', (event) => {
      if (this.pressed) {
        this.pressed = false;
      }
    });
  }
}